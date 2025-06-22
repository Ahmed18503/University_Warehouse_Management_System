<?php

namespace App\Livewire\Admin\ProductDeletionRequests;

use App\Models\ProductDeletionRequest;
use App\Models\WarehouseProduct;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Edit extends Component
{
    public ProductDeletionRequest $productDeletionRequest;
    public $newStatus;

    protected function rules(): array
    {
        return [
            'newStatus' => 'required|in:pending,approved,rejected',
        ];
    }

    function mount($id)
    {
        $this->productDeletionRequest = ProductDeletionRequest::with(['product', 'warehouse', 'requester', 'approver'])->findOrFail($id);
        $this->newStatus = $this->productDeletionRequest->status;
    }

    public function save()
    {
        $this->validate();

        // Permission check for approving/rejecting requests
        if (!auth()->user()->hasPermission('approve product deletion requests') && in_array($this->newStatus, ['approved', 'rejected'])) {
            $this->dispatch('done', error: "You do not have permission to approve or reject product deletion requests.");
            return;
        }

        DB::beginTransaction();
        try {
            if ($this->newStatus === 'approved' && $this->productDeletionRequest->status === 'pending') {
                // Deduct quantity from warehouse_products
                $warehouseProduct = WarehouseProduct::where('warehouse_id', $this->productDeletionRequest->warehouse_id)
                                                    ->where('product_id', $this->productDeletionRequest->product_id)
                                                    ->first();

                if (!$warehouseProduct || $warehouseProduct->quantity_good < $this->productDeletionRequest->quantity_to_delete) {
                    throw new \Exception("Insufficient stock for product '{$this->productDeletionRequest->product->name}' in the warehouse.");
                }

                // Calculate new quantity and update directly using Query Builder
                $newQuantity = $warehouseProduct->quantity_good - $this->productDeletionRequest->quantity_to_delete;
                DB::table('warehouse_products')
                    ->where('warehouse_id', $this->productDeletionRequest->warehouse_id)
                    ->where('product_id', $this->productDeletionRequest->product_id)
                    ->update(['quantity_good' => $newQuantity]);

                $this->productDeletionRequest->status = 'approved';
                $this->productDeletionRequest->approved_by_user_id = auth()->id();
                $this->productDeletionRequest->save();

                session()->flash('success', 'Product Deletion Request Approved and Quantity Deducted!');
            } elseif ($this->newStatus === 'rejected' && $this->productDeletionRequest->status === 'pending') {
                $this->productDeletionRequest->status = 'rejected';
                $this->productDeletionRequest->approved_by_user_id = auth()->id(); // Manager who rejects
                $this->productDeletionRequest->save();
                session()->flash('success', 'Product Deletion Request Rejected!');
            } else {
                // If trying to change status to pending from approved/rejected, or no change, just save.
                $this->productDeletionRequest->save();
            }
            DB::commit();
            return $this->redirect(route('admin.product-deletion-requests.index'), navigate: true);
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', $th->getMessage());
            // Keep the user on the same page to show the error more directly
        }
    }

    public function render()
    {
        return view('livewire.admin.product-deletion-requests.edit');
    }
} 