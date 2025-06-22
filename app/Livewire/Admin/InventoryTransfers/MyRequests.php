<?php

namespace App\Livewire\Admin\InventoryTransfers;

use App\Models\InventoryTransfer;
use Livewire\Component;
use Livewire\WithPagination;

class MyRequests extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    protected $queryString = ['search' => ['except' => '', 'as' => 's'], 'perPage'];

    public function render()
    {
        $userWarehouseId = auth()->user()->warehouse_id;

        $myRequests = InventoryTransfer::query()
            ->where(function ($query) use ($userWarehouseId) {
                $query->where('source_warehouse_id', $userWarehouseId)
                      ->orWhere('destination_warehouse_id', $userWarehouseId);
            })
            ->whereIn('status', [
                InventoryTransfer::STATUS_PENDING_SOURCE_APPROVAL,
                InventoryTransfer::STATUS_SOURCE_APPROVED,
                InventoryTransfer::STATUS_SHIPPED
            ])
            ->with(['sourceWarehouse', 'destinationWarehouse', 'creator', 'items'])
            ->when($this->search, function ($query) {
                $query->where(function ($query) {
                    $query->where('notes', 'like', '%' . $this->search . '%')
                          ->orWhereHas('sourceWarehouse', function ($q) {
                              $q->where('name', 'like', '%' . $this->search . '%');
                          })
                          ->orWhereHas('destinationWarehouse', function ($q) {
                              $q->where('name', 'like', '%' . $this->search . '%');
                          });
                });
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.inventory-transfers.my-requests', [
            'myRequests' => $myRequests,
        ]);
    }

    public function cancelTransfer(InventoryTransfer $inventoryTransfer)
    {
        if (!auth()->user()->hasPermission('manage inventory transfers')) {
            session()->flash('error', 'You do not have permission to cancel transfers.');
            return;
        }

        // Only allow cancellation if user is from destination warehouse and transfer is pending approval
        if ($inventoryTransfer->destination_warehouse_id !== auth()->user()->warehouse_id ||
            !in_array($inventoryTransfer->status, [
                InventoryTransfer::STATUS_PENDING_SOURCE_APPROVAL,
                InventoryTransfer::STATUS_SOURCE_APPROVED
            ])) {
            session()->flash('error', 'Transfer cannot be cancelled in its current state.');
            return;
        }

        $inventoryTransfer->status = InventoryTransfer::STATUS_CANCELLED;
        $inventoryTransfer->notes = trim($inventoryTransfer->notes . "\nCancelled by destination warehouse user: " . auth()->user()->name);
        $inventoryTransfer->cancelled_at = now();
            $inventoryTransfer->save();

        session()->flash('success', 'Transfer cancelled successfully.');
    }

    public function submitToSource(InventoryTransfer $inventoryTransfer)
    {
        // Check if the current user is the destination warehouse manager and has permission
        if (auth()->user()->warehouse_id != $inventoryTransfer->destination_warehouse_id || !auth()->user()->hasPermission('manage inventory transfers')) {
            session()->flash('error', 'You do not have permission to submit this request.');
            return;
        }

        // Ensure the request is in the correct state for submission
        if ($inventoryTransfer->status == 'pending_destination') {
            $inventoryTransfer->status = 'pending_source';
            $inventoryTransfer->save();
            session()->flash('success', 'Transfer request submitted to source warehouse for approval.');
        } else {
            session()->flash('error', 'This request cannot be submitted at its current status.');
        }
    }
} 