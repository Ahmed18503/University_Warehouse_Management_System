<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    function delete($id)
    {
        try {
            $userToDelete = User::findOrFail($id);
            if ($id == 1) {
                throw new \Exception("Error Processing request: This User is the super administrator", 1);
            }

            $currentUser = Auth::user();

            // Enforce warehouse-based management for non-super-admin users with 'manage users' permission
            if ($currentUser->hasPermission('manage users') && $currentUser->id !== 1) {
                if ($userToDelete->warehouse_id !== $currentUser->warehouse_id) {
                    throw new \Exception("You can only manage users within your own warehouse.", 1);
                }
            }

            $userToDelete->roles()->detach();
            $userToDelete->delete();

            $this->dispatch('done', success: "Successfully Deleted this User");
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }
    public function render()
    {
        $currentUser = Auth::user();
        $usersQuery = User::query();

        if ($currentUser && $currentUser->id !== 1 && $currentUser->hasPermission('manage users')) {
            $usersQuery->where('warehouse_id', $currentUser->warehouse_id);
        }

        return view('livewire.admin.users.index', [
            'users' => $usersQuery->get(),
        ]);
    }
}
