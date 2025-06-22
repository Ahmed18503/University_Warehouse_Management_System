<?php

namespace App\Livewire\Admin\ObsoleteConversions;

use App\Models\ObsoleteConversionRequest;
use App\Models\WarehouseProduct;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Edit extends Component
{
    public ObsoleteConversionRequest $obsoleteConversionRequest;
    public $newStatus;

    protected function rules(): array
    {
        return [
            'newStatus' => 'required|in:pending,approved,rejected',
        ];
    }

    public function mount($id)
    {
        $this->obsoleteConversionRequest = ObsoleteConversionRequest::with(['product', 'warehouse', 'requester', 'approver'])->findOrFail($id);
        $this->newStatus = $this->obsoleteConversionRequest->status;
    }

    public function save()
    {
        $this->validate();

        // Optional: Add permission check for approving/rejecting requests here if needed

        DB::beginTransaction();
        try {
            if ($this->newStatus === 'approved' && $this->obsoleteConversionRequest->status === 'pending') {
                // Re-fetch current stock for that product in that warehouse
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $this->obsoleteConversionRequest->warehouse_id)
                                                    ->where('product_id', $this->obsoleteConversionRequest->product_id)
                                                    ->first();

                if (!$warehouseProduct || $warehouseProduct->quantity_good < $this->obsoleteConversionRequest->quantity) {
                    throw new \Exception("Insufficient good stock for product '{$this->obsoleteConversionRequest->product->name}' in the warehouse.");
                }

                // Subtract the requested quantity from the main stock record
                DB::table('warehouse_products')
                    ->where('warehouse_id', $this->obsoleteConversionRequest->warehouse_id)
                    ->where('product_id', $this->obsoleteConversionRequest->product_id)
                    ->decrement('quantity_good', $this->obsoleteConversionRequest->quantity);

                // Add the same quantity to the obsolete stock record.
                DB::table('warehouse_products')
                    ->where('warehouse_id', $this->obsoleteConversionRequest->warehouse_id)
                    ->where('product_id', $this->obsoleteConversionRequest->product_id)
                    ->increment('quantity_obsolete', $this->obsoleteConversionRequest->quantity);

                $this->obsoleteConversionRequest->status = 'approved';
                $this->obsoleteConversionRequest->approved_by_user_id = auth()->id();
                $this->obsoleteConversionRequest->approved_at = now();
                $this->obsoleteConversionRequest->save();

                session()->flash('success', 'Obsolete Conversion Request Approved and Quantity Deducted!');
            } elseif ($this->newStatus === 'rejected' && $this->obsoleteConversionRequest->status === 'pending') {
                $this->obsoleteConversionRequest->status = 'rejected';
                $this->obsoleteConversionRequest->approved_by_user_id = auth()->id();
                $this->obsoleteConversionRequest->approved_at = now();
                $this->obsoleteConversionRequest->save();
                session()->flash('success', 'Obsolete Conversion Request Rejected!');
            } else {
                // If trying to change status to pending from approved/rejected, or no change, just save.
                $this->obsoleteConversionRequest->save();
                session()->flash('info', 'Obsolete Conversion Request updated.');
            }

            DB::commit();
            return $this->redirect(route('admin.obsolete-conversions.index'), navigate: true);
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', "Something Went Wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.obsolete-conversions.edit');
    }
}
