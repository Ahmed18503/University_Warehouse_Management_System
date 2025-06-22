<div>
    <x-slot:header>ملخص المخزون</x-slot:header>

    <div class="row mb-3">
        <!-- Inventory Health Overview -->
        <div class="col-md-12">
            <h4 class="text-primary mb-3">ملخص صحة المخزون <small class="text-muted">(لمخزنك)</small></h4>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-info text-white">
                <div class="card-body">
                    <i class="bi bi-box-seam display-4"></i>
                    <h5 class="card-title mt-2">إجمالي المنتجات الفريدة</h5>
                    <p class="fs-3 fw-bold">{{ number_format($totalUniqueProducts) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-success text-white">
                <div class="card-body">
                    <i class="bi bi-boxes display-4"></i>
                    <h5 class="card-title mt-2">إجمالي الكمية الجيدة</h5>
                    <p class="fs-3 fw-bold">{{ number_format($totalGoodQuantity) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <i class="bi bi-recycle display-4"></i>
                    <h5 class="card-title mt-2">إجمالي الكمية الكهنة</h5>
                    <p class="fs-3 fw-bold">{{ number_format($totalObsoleteQuantity) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-danger text-white">
                <div class="card-body">
                    <i class="bi bi-arrow-down-circle display-4"></i>
                    <h5 class="card-title mt-2">منتجات تحت الحد الأدنى</h5>
                    <p class="fs-3 fw-bold">{{ number_format($lowStockProductsCount) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-primary text-white">
                <div class="card-body">
                    <i class="bi bi-clipboard-check display-4"></i>
                    <h5 class="card-title mt-2">تدقيقات مكتملة (آخر 30 يومًا)</h5>
                    <p class="fs-3 fw-bold">{{ number_format($auditsCompletedLast30Days) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm bg-secondary text-white">
                <div class="card-body">
                    <i class="bi bi-exclamation-triangle display-4"></i>
                    <h5 class="card-title mt-2">تدقيقات بها اختلافات (آخر 30 يومًا)</h5>
                    <p class="fs-3 fw-bold">{{ number_format($auditsWithDiscrepanciesLast30Days) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-12 mb-3">
            <div class="card text-center shadow-sm bg-dark text-white">
                <div class="card-body">
                    <i class="bi bi-funnel display-4"></i>
                    <h5 class="card-title mt-2">إجمالي الاختلافات (آخر 30 يومًا)</h5>
                    <p class="fs-3 fw-bold">{{ number_format($totalDiscrepancyAmount) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
