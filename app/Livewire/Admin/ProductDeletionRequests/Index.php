<?php

namespace App\Livewire\Admin\ProductDeletionRequests;

use App\Models\ProductDeletionRequest;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function delete($id)
    {
        try {
            $request = ProductDeletionRequest::findOrFail($id);

            // Only allow deletion of requests in 'pending' or 'rejected' status
            if (!in_array($request->status, ['pending', 'rejected'])) {
                throw new \Exception("Cannot delete a request that is already approved.", 1);
            }

            $request->delete();

            $this->dispatch('done', success: "Successfully deleted this Product Deletion Request.");
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.product-deletion-requests.index', [
            'deletionRequests' => ProductDeletionRequest::orderBy('id', 'DESC')->paginate(10)
        ]);
    }
} 