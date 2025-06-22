<?php

namespace App\Livewire\Admin\Roles;

use App\Models\Role;
use Livewire\Component;

class Edit extends Component
{
    public Role $role;
    public array $permissions = [];
    public array $arabic_permissions = [];

    public array $selected_permissions = [];

    function rules()
    {
        return [
            'role.title' => "required",
        ];
    }

    function mount($id)
    {
        $this->role = Role::find($id);
        $this->selected_permissions = json_decode($this->role->permissions);
        $this->permissions = config('permissions.permissions');
        $this->arabic_permissions = config('permissions.arabic_permissions');
    }

    function add($permission)
    {
        // $this->dispatch('done', success: "Test Complete");
        try {
            if (in_array($permission, $this->selected_permissions)) {
                throw new \Exception("Error Processing Request: Permission already added", 1);
            }

            array_push($this->selected_permissions, $permission);
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }
    function subtract($key)
    {
        try {
            //code...
            array_splice($this->selected_permissions, $key, 1);
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }

    function updated()
    {
        $this->validate();
    }

    function save()
    {
        $this->validate();
        try {
            if (in_array('manage roles', $this->selected_permissions) && $this->role->id !== 1) {
                throw new \Exception("The 'manage roles' permission can only be assigned to the Super Administrator role.", 1);
            }

            if ($this->role->id === 1 && !in_array('manage roles', $this->selected_permissions)) {
                throw new \Exception("The Super Administrator role must always have the 'manage roles' permission.", 1);
            }

            $this->role->permissions = json_encode($this->selected_permissions);
            $this->role->update();
            return redirect()->route('admin.roles.index');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.admin.roles.edit', [
            'permissions' => $this->permissions,
            'arabic_permissions' => $this->arabic_permissions
        ]);
    }
}
