<div>
    <x-slot:header>ملخص الحسابات وتحليل المخزون</x-slot:header>

    <!-- Inventory and Audit Analysis -->
    <div class="row mb-3">
        <div class="col-md-12">
            <h4 class="text-primary mb-3">تحليل المخزون والتدقيق <small class="text-muted">(لمخزنك)</small></h4>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-info text-white">
                <div class="card-body">
                    <i class="bi bi-box-seam display-4"></i>
                    <h5 class="card-title mt-2">إجمالي المنتجات في المخزن</h5>
                    <p class="fs-3 fw-bold">{{ number_format($totalWarehouseProducts) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-success text-white">
                <div class="card-body">
                    <i class="bi bi-cash-stack display-4"></i>
                    <h5 class="card-title mt-2">قيمة المخزون الجيد</h5>
                    <p class="fs-3 fw-bold">KES {{ number_format($totalGoodStockValue, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <i class="bi bi-recycle display-4"></i>
                    <h5 class="card-title mt-2">قيمة المخزون الكهنة</h5>
                    <p class="fs-3 fw-bold">KES {{ number_format($totalObsoleteStockValue, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-danger text-white">
                <div class="card-body">
                    <i class="bi bi-funnel display-4"></i>
                    <h5 class="card-title mt-2">إجمالي الاختلافات (جميع التدقيقات)</h5>
                    <p class="fs-3 fw-bold">{{ number_format($totalOverallDiscrepancy, 2) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-primary text-white">
                <div class="card-body">
                    <i class="bi bi-check-circle display-4"></i>
                    <h5 class="card-title mt-2">عدد الأصناف المعدلة (من التدقيقات)</h5>
                    <p class="fs-3 fw-bold">{{ number_format($totalAdjustedItemsCount) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-secondary text-white">
                <div class="card-body">
                    <i class="bi bi-clock-history display-4"></i>
                    <h5 class="card-title mt-2">آخر تدقيق مكتمل</h5>
                    @if ($latestCompletedAudit)
                        <p class="fs-5 mb-0">بتاريخ: {{ Carbon\Carbon::parse($latestCompletedAudit->audit_date)->format('d M, Y') }}</p>
                        <p class="fs-5">بواسطة: {{ $latestCompletedAudit->auditor->name ?? 'N/A' }}</p>
                    @else
                        <p class="fs-5">لا يوجد تدقيقات مكتملة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
