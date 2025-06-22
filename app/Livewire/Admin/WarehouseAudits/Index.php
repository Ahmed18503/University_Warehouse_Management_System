<?php

namespace App\Livewire\Admin\WarehouseAudits;

use App\Models\WarehouseAudit;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $searchDate = '';
    public $searchStatus = '';
    public $perPage = 10;

    protected $queryString = [
        'searchDate' => ['except' => '', 'as' => 'date'],
        'searchStatus' => ['except' => '', 'as' => 'status'],
        'perPage' => ['except' => 10, 'as' => 'per_page'],
    ];

    private function getStatusList(): array
    {
        return [
            WarehouseAudit::STATUS_IN_PROGRESS => 'قيد التنفيذ',
            WarehouseAudit::STATUS_COMPLETED => 'مكتمل',
            WarehouseAudit::STATUS_CANCELLED => 'ملغى',
        ];
    }

    public function getStatusBadgeClass($status): string
    {
        return match($status) {
            WarehouseAudit::STATUS_IN_PROGRESS => 'bg-info',
            WarehouseAudit::STATUS_COMPLETED => 'bg-success',
            WarehouseAudit::STATUS_CANCELLED => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    public function updatingSearchDate()
    {
        $this->resetPage();
    }

    public function updatingSearchStatus()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        try {
            $auditToDelete = WarehouseAudit::findOrFail($id);
            $currentUser = Auth::user();

            if ($currentUser->warehouse_id && $auditToDelete->warehouse_id != $currentUser->warehouse_id) {
                throw new \Exception("يمكنك فقط إلغاء التدقيقات في مخزنك.", 1);
            }
            
            if (!$auditToDelete->canBeCancelled()) {
                throw new \Exception("لا يمكن إلغاء هذا التدقيق في حالته الحالية.", 1);
            }

            DB::beginTransaction();
            $auditToDelete->cancel();
            DB::commit();

            session()->flash('success', 'تم إلغاء التدقيق بنجاح.');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', "حدث خطأ: " . $th->getMessage());
        }
    }

    public function render()
    {
        $currentUser = Auth::user();
        
        $auditsQuery = WarehouseAudit::query()
            ->when($currentUser->warehouse_id, function ($query) use ($currentUser) {
                $query->where('warehouse_id', $currentUser->warehouse_id);
            })
            ->when($this->searchDate, function ($query) {
                $query->whereDate('audit_date', $this->searchDate);
            })
            ->when($this->searchStatus, function ($query) {
                $query->where('status', $this->searchStatus);
            })
            ->with(['auditor', 'items', 'warehouse'])
            ->orderBy('audit_date', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.warehouse-audits.index', [
            'audits' => $auditsQuery,
            'statuses' => $this->getStatusList(),
        ]);
    }
}
