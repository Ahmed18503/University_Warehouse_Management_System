<?php

namespace App\Livewire\Admin\Users;

use App\Mail\UserCreatedMail;
use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public User $user;
    public $selectedRoles = [];
    public $warehouse_id;

    function rules()
    {
        return [
            'user.name' => "required",
            'selectedRoles' => "required",
            'user.email' => "required|unique:users,email",
            'warehouse_id' => "required|exists:warehouse,id",
        ];
    }

    function mount()
    {
        $this->user = new User();
        // Set default warehouse_id if current user has manage users permission and is not super admin
        $currentUser = Auth::user();
        if ($currentUser && $currentUser->id !== 1 && $currentUser->hasPermission('manage users')) {
            $this->warehouse_id = $currentUser->warehouse_id;
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
            // Prevent assigning role_id 1 to any new user
            if (in_array(1, $this->selectedRoles)) {
                throw new \Exception("The Super Administrator role can only be assigned to the user with ID 1.", 1);
            }

            $currentUser = Auth::user();
            // Enforce warehouse-based creation for non-super-admin users with 'manage users' permission
            if ($currentUser->hasPermission('manage users') && $currentUser->id !== 1) {
                if ($this->warehouse_id !== $currentUser->warehouse_id) {
                    throw new \Exception("You can only create users within your own warehouse.", 1);
                }
            }

            $password = 'password';
            $this->user->password = Hash::make($password);
            $this->user->warehouse_id = $this->warehouse_id;
            $this->user->save();

            $this->user->roles()->attach($this->selectedRoles);

            Mail::to($this->user->email)->send(new UserCreatedMail($this->user, $password));

            return redirect()->route('admin.users.index');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.users.create', [
            'roles' => Role::all(),
            'warehouses' => Warehouse::all()
        ]);
    }
}
