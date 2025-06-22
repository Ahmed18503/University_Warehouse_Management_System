<?php

namespace App\Livewire\Admin\Warehouses;

use App\Models\Warehouse;
use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    function delete($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            if ($id == 1) {
                throw new \Exception("Error Processing request: This Warehouse is the main warehouse", 1);
            }

            // Check if there are users assigned to this warehouse
            $usersCount = User::where('warehouse_id', $id)->count();
            if ($usersCount > 0) {
                throw new \Exception("Cannot delete warehouse: There are {$usersCount} users assigned to this warehouse. Please reassign these users to another warehouse first.", 1);
            }

            $warehouse->delete();

            $this->dispatch('done', success: "Successfully Deleted this Warehouse");
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.admin.warehouses.index', [
            'warehouses' => Warehouse::all(),
        ]);
    }
}
