<?php

namespace App\Livewire\Admin\Warehouses;

use App\Models\Warehouse;
use Livewire\Component;

class Create extends Component
{
    public Warehouse $warehouse;

    function rules()
    {
        return [
            'warehouse.name' => "required",
        ];
    }

    function mount()
    {
        $this->warehouse = new Warehouse();
    }

    function updated()
    {
        $this->validate();
    }

    function save()
    {
        $this->validate();
        try {
            $this->warehouse->save();

            return redirect()->route('admin.warehouses.index');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.admin.warehouses.create', [
            'warehouses' => Warehouse::all()
        ]);
    }
}
