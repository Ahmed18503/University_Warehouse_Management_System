<?php

namespace App\Livewire\Admin;

use App\Models\WarehouseAudit;
use App\Models\WarehouseProduct;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // No longer needed properties for financial data
    // public $month;
    // public $total_revenue = 0;
    // public $revenueDeviation = 0;
    // public $lineChartModel;

    // New properties for Inventory Dashboard (retained)
    public $totalUniqueProducts = 0;
    public $totalGoodQuantity = 0;
    public $totalObsoleteQuantity = 0;
    public $lowStockProductsCount = 0;
    public $auditsCompletedLast30Days = 0;
    public $auditsWithDiscrepanciesLast30Days = 0;
    public $totalDiscrepancyAmount = 0;

    // No longer needed listeners for chart interaction
    // protected $listeners = [
    //     'onPointClick' => 'handleOnPointClick',
    //     'onSliceClick' => 'handleOnSliceClick',
    //     'onColumnClick' => 'handleOnColumnClick',
    // ];

    public function mount()
    {
        // $this->month = Carbon::now()->format('Y-m'); // No longer needed for financial chart
        // $this->generateChart(); // No longer needed
        $this->loadInventoryMetrics();
    }

    public function updatedMonth()
    {
        // $this->generateChart(); // No longer needed
        $this->loadInventoryMetrics();
    }

    private function loadInventoryMetrics()
    {
        $userWarehouseId = auth()->user()->warehouse_id;

        // Total unique products in user's warehouse
        $this->totalUniqueProducts = WarehouseProduct::where('warehouse_id', $userWarehouseId)
                                                    ->distinct('product_id')
                                                    ->count();

        // Total good and obsolete quantities
        $this->totalGoodQuantity = WarehouseProduct::where('warehouse_id', $userWarehouseId)->sum('quantity_good');
        $this->totalObsoleteQuantity = WarehouseProduct::where('warehouse_id', $userWarehouseId)->sum('quantity_obsolete');

        // Low stock products count (assuming min_stock_level applies to quantity_good)
        $this->lowStockProductsCount = WarehouseProduct::where('warehouse_id', $userWarehouseId)
                                                        ->whereColumn('quantity_good', '<', 'min_stock_level')
                                                        ->count();

        // Audits completed in the last 30 days
        $this->auditsCompletedLast30Days = WarehouseAudit::where('warehouse_id', $userWarehouseId)
                                                            ->where('status', 'completed')
                                                            ->where('audit_date', '>=', Carbon::now()->subDays(30))
                                                            ->count();

        // Audits with discrepancies in the last 30 days
        $this->auditsWithDiscrepanciesLast30Days = WarehouseAudit::where('warehouse_id', $userWarehouseId)
                                                                ->where('audit_date', '>=', Carbon::now()->subDays(30))
                                                                ->whereHas('items', function ($query) {
                                                                    $query->whereRaw('system_qty_good != counted_qty_good OR system_qty_obsolete != counted_qty_obsolete');
                                                                })
                                                                ->count();

        // Total discrepancy amount (sum of absolute discrepancies from completed/adjusted audits in last 30 days)
        $this->totalDiscrepancyAmount = WarehouseAudit::where('warehouse_id', $userWarehouseId)
                                                    ->whereIn('status', ['completed', 'adjusted'])
                                                    ->where('audit_date', '>=', Carbon::now()->subDays(30))
                                                    ->withSum('items as total_discrepancy_value', DB::raw('ABS(counted_qty_good - system_qty_good) * unit_cost + ABS(counted_qty_obsolete - system_qty_obsolete) * unit_cost'))
                                                    ->get()
                                                    ->sum('total_discrepancy_value');
    }

    // No longer needed methods for chart generation or document download
    // public function generateChart()
    // {
    //     // ... (removed financial chart logic) ...
    // }

    // public function handleOnPointClick($point)
    // {
    //     // Handle point click if needed
    // }

    // public function handleOnSliceClick($slice)
    // {
    //     // Handle slice click if needed
    // }

    // public function handleOnColumnClick($column)
    // {
    //     // Handle column click if needed
    // }

    // public function downloadPLStatement($month)
    // {
    //     // Existing logic
    // }

    // public function downloadAccountSummary($month)
    // {
    //     // Existing logic
    // }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
