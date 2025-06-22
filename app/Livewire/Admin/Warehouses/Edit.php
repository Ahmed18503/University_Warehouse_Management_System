<?php

namespace App\Livewire\Admin\Warehouses;

use App\Models\Warehouse;
use Livewire\Component;

class Edit extends Component
{
    public Warehouse $warehouse;

    function rules()
    {
        return [
            'warehouse.name' => "required",
        ];
    }

    function mount($id)
    {
        $this->warehouse = Warehouse::find($id);
    }

    function updated()
    {
        $this->validate();
    }

    function save()
    {
        $this->validate();
        try {
            $this->warehouse->update();
            return redirect()->route('admin.warehouses.index');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.admin.warehouses.edit', [
            'warehouses' => Warehouse::all()
        ]);
    }
}
