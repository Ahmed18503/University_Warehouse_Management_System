<?php

namespace App\Livewire\Admin;

use App\Models\WarehouseProduct;
use App\Models\WarehouseAudit;
use App\Models\WarehouseAuditItem;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class AccountsSummary extends Component
{
    // Removed properties for financial data
    // public $total_revenue = 0;
    // public $revenueDeviation = 0;
    // public $revenuePrevious = 0;
    // public $sales_count = 0;
    // public $stock_value = 0;

    // public $receivables = 0;
    // public $overdue_invoices = 0;
    // public $loss_summary = 0;

    public $instance;
    public $month;
    // public $previousMonth; // No longer directly used for deviation

    // Retained properties for detailed Inventory Analysis
    public $totalWarehouseProducts = 0;
    public $totalGoodStockValue = 0;
    public $totalObsoleteStockValue = 0;
    public $totalOverallDiscrepancy = 0;
    public $latestCompletedAudit = null;
    public $totalAdjustedItemsCount = 0;

    function updatedMonth()
    {
        $this->instance = Carbon::parse($this->month);
        // Removed financial calculations
        // $this->total_revenue = $this->getTotalRevenue($this->instance->format('Y-m'));
        // $this->revenuePrevious = $this->getTotalRevenue($this->instance->copy()->subMonth()->format('Y-m'));

        // if ($this->total_revenue !== 0) {
        //     $this->revenueDeviation = ($this->total_revenue - $this->revenuePrevious) / $this->total_revenue;
        // } else {
        //     $this->revenueDeviation = ($this->revenuePrevious === 0) ? 0 : -1;
        // }

        $this->loadDetailedInventoryAnalysis();
    }

    // Removed financial helper functions
    // function getOpeningStock($month) { /* ... */ }
    // function getClosingStock($month) { /* ... */ }
    // function getTotalRevenue($month) { /* ... */ }
    // function getTotalPurchases($month) { /* ... */ }
    // function getTotalSalesPayments($month) { /* ... */ }
    // function getTotalPurchasePayments($month) { /* ... */ }

    private function loadDetailedInventoryAnalysis()
    {
        $userWarehouseId = auth()->user()->warehouse_id;

        // Total products (good + obsolete) in the user's warehouse
        $this->totalWarehouseProducts = WarehouseProduct::where('warehouse_id', $userWarehouseId)
                                                        ->sum(DB::raw('quantity_good + quantity_obsolete'));

        // Total value of good and obsolete stock
        $this->totalGoodStockValue = WarehouseProduct::where('warehouse_id', $userWarehouseId)
                                                    ->join('products', 'warehouse_products.product_id', '=', 'products.id')
                                                    ->sum(DB::raw('warehouse_products.quantity_good * products.price'));

        $this->totalObsoleteStockValue = WarehouseProduct::where('warehouse_id', $userWarehouseId)
                                                        ->join('products', 'warehouse_products.product_id', '=', 'products.id')
                                                        ->sum(DB::raw('warehouse_products.quantity_obsolete * products.price'));

        // Overall discrepancy from all completed/adjusted audits for this warehouse
        $this->totalOverallDiscrepancy = WarehouseAuditItem::whereHas('audit', function ($query) use ($userWarehouseId) {
                                                            $query->where('warehouse_id', $userWarehouseId)
                                                                  ->whereIn('status', ['completed', 'adjusted']);
                                                        })
                                                        ->sum('discrepancy');

        // Latest completed audit for this warehouse
        $this->latestCompletedAudit = WarehouseAudit::where('warehouse_id', $userWarehouseId)
                                                    ->whereIn('status', ['completed', 'adjusted'])
                                                    ->with('auditor')
                                                    ->latest('audit_date')
                                                    ->first();
        
        // Total adjusted items count (from adjusted audits)
        $this->totalAdjustedItemsCount = WarehouseAuditItem::whereHas('audit', function ($query) use ($userWarehouseId) {
                                                            $query->where('warehouse_id', $userWarehouseId)
                                                                  ->where('status', 'adjusted');
                                                        })
                                                        ->where('discrepancy', '!=', 0)
                                                        ->count();
    }

    // Removed download functions
    // function downloadPLStatement($month) { /* ... */ }
    // function downloadAccountSummary($month) { /* ... */ }

    function mount()
    {
        $this->instance = Carbon::now();
        $this->month = $this->instance->format('Y-m');
        // Removed financial calculations from mount
        // $this->total_revenue = $this->getTotalRevenue($this->instance->format('Y-m'));
        // $this->revenuePrevious = $this->getTotalRevenue($this->instance->copy()->subMonth()->format('Y-m'));

        // if ($this->total_revenue  !== 0) {
        //     $this->revenueDeviation = ($this->total_revenue - $this->revenuePrevious) / $this->total_revenue;
        // } else {
        //     $this->revenueDeviation = ($this->revenuePrevious === 0) ? 0 : -1;
        // }
        $this->loadDetailedInventoryAnalysis();
    }

    // Removed chart generation function
    // function getSalesChart() { /* ... */ }

    public function render()
    {
        // Removed line chart model passing
        // $lineChartModel = $this->getSalesChart();
        return view('livewire.admin.accounts-summary', [
            // 'lineChartModel' => $lineChartModel,
        ]);
    }
}
