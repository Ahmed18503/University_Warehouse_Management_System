<?php

namespace App\Livewire\Admin\Users;

use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Edit extends Component
{
    public User $user;
    public $selectedRoles = [];
    public $warehouse_id;

    function rules()
    {
        return [
            'user.name' => "required",
            'selectedRoles' => "required",
            'user.email' => "required|unique:users,email," . $this->user->id,
            'warehouse_id' => "required|exists:warehouse,id",
        ];
    }

    function mount($id)
    {
        $this->user = User::find($id);
        $this->selectedRoles = $this->user->roles()->pluck('id')->toArray();
        $this->warehouse_id = $this->user->warehouse_id;

        $currentUser = Auth::user();
        if ($currentUser && $currentUser->id !== 1 && $currentUser->hasPermission('manage users')) {
            if ($this->user->warehouse_id !== $currentUser->warehouse_id) {
                return redirect()->route('admin.users.index'); // Redirect if not authorized to edit this user
            }
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
            // Prevent assigning role_id 1 to any user other than user_id 1
            if (in_array(1, $this->selectedRoles) && $this->user->id !== 1) {
                throw new \Exception("The Super Administrator role can only be assigned to the user with ID 1.", 1);
            }

            // Prevent removing role_id 1 from user_id 1
            if ($this->user->id === 1 && !in_array(1, $this->selectedRoles)) {
                throw new \Exception("The Super Administrator role cannot be removed from the user with ID 1.", 1);
            }

            $currentUser = Auth::user();
            // Enforce warehouse-based editing for non-super-admin users with 'manage users' permission
            if ($currentUser->hasPermission('manage users') && $currentUser->id !== 1) {
                // Prevent changing the warehouse of the user being edited
                if ($this->warehouse_id !== $this->user->warehouse_id) {
                    throw new \Exception("You cannot change the warehouse of this user.", 1);
                }
            }

            $this->user->warehouse_id = $this->warehouse_id;
            $this->user->update();
            $this->user->roles()->detach();
            $this->user->roles()->attach($this->selectedRoles);
            return redirect()->route('admin.users.index');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.edit', [
            'roles' => Role::all(),
            'warehouses' => Warehouse::all(),
            'currentUser' => Auth::user(),
        ]);
    }
}
