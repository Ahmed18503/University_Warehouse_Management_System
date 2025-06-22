<?php

namespace App\Livewire\Admin\ObsoleteConversions;

use App\Models\ObsoleteConversionRequest;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $pendingRequests = ObsoleteConversionRequest::with(['product', 'warehouse', 'requester'])
                                                    ->where('status', 'pending')
                                                    ->latest()
                                                    ->get();

        return view('livewire.admin.obsolete-conversions.index', [
            'pendingRequests' => $pendingRequests,
        ]);
    }
}
