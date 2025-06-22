<?php

namespace App\Livewire\Admin\WarehouseAudits;

use App\Models\WarehouseAudit;
use App\Models\WarehouseProduct;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Show extends Component
{
    use AuthorizesRequests;

    public WarehouseAudit $warehouseAudit;
    public $items = [];

    protected function rules()
    {
        return [
            'items.*.counted_qty_good' => 'required|integer|min:0',
            'items.*.counted_qty_obsolete' => 'required|integer|min:0',
            'items.*.notes' => 'nullable|string|max:1000',
        ];
    }

    public function mount(int $warehouse_audit)
    {
        // Fetch the model directly using the provided ID.
        $this->warehouseAudit = WarehouseAudit::find($warehouse_audit);

        if (is_null($this->warehouseAudit)) {
            session()->flash('error', 'The requested audit could not be found.');
            return $this->redirect(route('admin.warehouse-audits.index'), navigate: true);
        }

        // Enforce the rule that a user can only see audits for their own warehouse.
        if (auth()->user()->warehouse_id != $this->warehouseAudit->warehouse_id) {
            abort(403, 'You are not authorized to view this audit.');
        }

        $this->warehouseAudit->load(['items.product.unit', 'auditor', 'warehouse']);

        foreach ($this->warehouseAudit->items as $item) {
            $this->items[$item->id] = [
                'counted_qty_good' => $item->counted_qty_good,
                'counted_qty_obsolete' => $item->counted_qty_obsolete,
                'notes' => $item->notes,
            ];
        }
    }

    public function save()
    {
        if (!$this->warehouseAudit->isEditable()) {
            session()->flash('error', 'لا يمكن تعديل هذا التدقيق في حالته الحالية.');
            return;
        }

        $this->validate();

        DB::beginTransaction();
        try {
            foreach ($this->items as $itemId => $itemData) {
                $this->warehouseAudit->items()->where('id', $itemId)->update([
                    'counted_qty_good' => $itemData['counted_qty_good'],
                    'counted_qty_obsolete' => $itemData['counted_qty_obsolete'],
                    'notes' => $itemData['notes'],
                ]);
            }
            DB::commit();
            $this->warehouseAudit->refresh();
            session()->flash('success', 'تم حفظ تقدم التدقيق بنجاح.');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', "حدث خطأ: " . $th->getMessage());
        }
    }

    public function completeAudit()
    {
        // First, save any pending changes.
        $this->save();
        
        // Then, mark the audit as complete.
        DB::beginTransaction();
        try {
            // Update the actual warehouse quantities.
            foreach ($this->warehouseAudit->items as $item) {
                WarehouseProduct::updateOrCreate(
                    [
                        'warehouse_id' => $this->warehouseAudit->warehouse_id,
                        'product_id' => $item->product_id,
                    ],
                    [
                        'quantity_good' => $item->counted_qty_good,
                        'quantity_obsolete' => $item->counted_qty_obsolete,
                    ]
                );
            }
            
            $this->warehouseAudit->complete();
            
            DB::commit();
            $this->warehouseAudit->refresh();
            session()->flash('success', 'اكتمل التدقيق بنجاح');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', "حدث خطأ أثناء إكمال التدقيق: " . $th->getMessage());
        }
    }

    public function getStatusBadgeClass()
    {
        return match($this->warehouseAudit->status) {
            WarehouseAudit::STATUS_IN_PROGRESS => 'bg-info',
            WarehouseAudit::STATUS_COMPLETED => 'bg-success',
            WarehouseAudit::STATUS_CANCELLED => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function render()
    {
        return view('livewire.admin.warehouse-audits.show');
    }
}
