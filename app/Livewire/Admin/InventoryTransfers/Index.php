<?php

namespace App\Livewire\Admin\InventoryTransfers;

use App\Models\InventoryTransfer;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function delete($id)
    {
        try {
            $transfer = InventoryTransfer::findOrFail($id);

            // Only allow deletion of transfers in draft status
            if ($transfer->status !== InventoryTransfer::STATUS_DRAFT) {
                throw new \Exception("Only draft transfers can be deleted.");
            }

            if (!auth()->user()->hasPermission('manage inventory transfers')) {
                throw new \Exception("You do not have permission to delete transfers.");
            }

            $transfer->delete();

            session()->flash('success', 'Transfer deleted successfully.');
        } catch (\Throwable $th) {
            session()->flash('error', $th->getMessage());
        }
    }

    public function render()
    {
        $userWarehouseId = auth()->user()->warehouse_id;

        $inventoryTransfers = InventoryTransfer::with([
                'sourceWarehouse', 
                'destinationWarehouse', 
                'creator',
                'items'
            ])
            ->where(function ($query) use ($userWarehouseId) {
                $query->where('source_warehouse_id', $userWarehouseId)
                      ->orWhere('destination_warehouse_id', $userWarehouseId);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return view('livewire.admin.inventory-transfers.index', [
            'inventoryTransfers' => $inventoryTransfers
        ]);
    }
} 