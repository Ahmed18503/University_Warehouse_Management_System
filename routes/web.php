<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;

Route::redirect('/', 'dashboard');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->name('admin.')->group(function () {


    /**
     * Order Download
     */
    Route::get('/{id}/order', function ($id) {
        $order = Order::find($id);

        return Pdf::loadView('pdf.purchase-order', [
            'order' => $order
        ])->stream();
        // ->stream();
        // ->download('Order - #' . sprintf('%04d',  $order->id) . '.pdf')
    })->name('order-download');


    /**
     * Quotation Download
     */
    Route::get('/{id}/quotation', function ($id) {
        $quotation = Quotation::find($id);

        return Pdf::loadView('pdf.quotation', [
            'quotation' => $quotation
        ])->stream();
        // ->stream();
        // ->download('Order - #' . sprintf('%04d',  $order->id) . '.pdf')
    })->name('quotation-download');


    /**
     * Invoice Download
     */
    Route::get('/{id}/invoice', function ($id) {
        $invoice = Invoice::find($id);

        return Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice
        ])->stream();
        // ->stream();
        // ->download('Order - #' . sprintf('%04d',  $order->id) . '.pdf')
    })->name('invoice-download');



    Route::get('/dashboard', Admin\Dashboard::class)->name('dashboard');
    // Route::get('/accounts-summary', Admin\AccountsSummary::class)->name('accounts-summary');

    Route::prefix('users')->middleware('permission:manage users')->name('users.')->group(function () {
        Route::get('/', Admin\Users\Index::class)->name('index');
        Route::get('/create', Admin\Users\Create::class)->name('create');
        Route::get('{id}/edit', Admin\Users\Edit::class)->name('edit');
    });

    Route::prefix('warehouses')->middleware('permission:manage warehouses')->name('warehouses.')->group(function () {
        Route::get('/', Admin\Warehouses\Index::class)->name('index');
        Route::get('/create', Admin\Warehouses\Create::class)->name('create');
        Route::get('{id}/edit', Admin\Warehouses\Edit::class)->name('edit');
    });

    Route::prefix('product-categories')->middleware('permission:manage product categories')->name('product-categories.')->group(function () {
        Route::get('/', Admin\ProductCategories\Index::class)->name('index');
        Route::get('/create', Admin\ProductCategories\Create::class)->name('create');
        Route::get('{id}/edit', Admin\ProductCategories\Edit::class)->name('edit');
    });

    Route::prefix('products')->middleware('permission:manage products')->name('products.')->group(function () {
        Route::get('/', Admin\Products\Index::class)->name('index');
        Route::get('/create', Admin\Products\Create::class)->name('create');
        Route::get('{id}/edit', Admin\Products\Edit::class)->name('edit');
    });

    Route::prefix('warehouse-products')->middleware('permission:manage products')->name('warehouse-products.')->group(function () {
        Route::get('/', Admin\WarehouseProducts\Index::class)->name('index');
        Route::get('/add', Admin\WarehouseProducts\Add::class)->name('add');
        // Route::get('{id}/edit', Admin\WarehouseProducts\Edit::class)->name('edit');
    });

    Route::prefix('all-warehouse-products')->middleware('permission:view all warehouse products')->name('all-warehouse-products.')->group(function () {
        Route::get('/', Admin\AllWarehouseProducts\Index::class)->name('index');
    });

    Route::prefix('roles')->middleware('permission:manage roles')->name('roles.')->group(function () {
        Route::get('/', Admin\Roles\Index::class)->name('index');
        Route::get('/create', Admin\Roles\Create::class)->name('create');
        Route::get('{id}/edit', Admin\Roles\Edit::class)->name('edit');
    });

    Route::prefix('suppliers')->middleware('permission:manage suppliers')->name('suppliers.')->group(function () {
        Route::get('/', Admin\Suppliers\Index::class)->name('index');
        Route::get('/create', Admin\Suppliers\Create::class)->name('create');
        Route::get('{id}/edit', Admin\Suppliers\Edit::class)->name('edit');
    });

    Route::prefix('units')->middleware('permission:manage units')->name('units.')->group(function () {
        Route::get('/', Admin\Units\Index::class)->name('index');
        Route::get('/create', Admin\Units\Create::class)->name('create');
        Route::get('{id}/edit', Admin\Units\Edit::class)->name('edit');
    });

    Route::prefix('inventory-transfers')->middleware('permission:manage inventory transfers')->name('inventory-transfers.')->group(function () {
        Route::get('/', Admin\InventoryTransfers\Index::class)->name('index');
        Route::get('/create', Admin\InventoryTransfers\Create::class)->name('create');
        Route::get('{id}/edit', Admin\InventoryTransfers\Edit::class)->name('edit');
        Route::get('/my-requests', Admin\InventoryTransfers\MyRequests::class)->name('my-requests');
        Route::get('/pending-confirmations', Admin\InventoryTransfers\PendingConfirmations::class)->name('pending-confirmations');
    });

    Route::prefix('product-deletion-requests')->middleware('permission:manage product deletion requests')->name('product-deletion-requests.')->group(function () {
        Route::get('/', Admin\ProductDeletionRequests\Index::class)->name('index');
        Route::get('/create', Admin\ProductDeletionRequests\Create::class)->name('create');
        Route::get('{id}/edit', Admin\ProductDeletionRequests\Edit::class)->name('edit');
    });

    Route::prefix('obsolete-conversions')->name('obsolete-conversions.')->group(function () {
        Route::get('/request', Admin\ObsoleteConversions\Create::class)->middleware('permission:request obsolete conversion')->name('create');
        Route::get('/', Admin\ObsoleteConversions\Index::class)->middleware('permission:approve obsolete conversion requests')->name('index');
        Route::get('{id}/edit', Admin\ObsoleteConversions\Edit::class)->middleware('permission:approve obsolete conversion requests')->name('edit');
    });

    // Inventory Audits Routes
    Route::prefix('warehouse-audits')->name('warehouse-audits.')->group(function () {
        Route::get('/', Admin\WarehouseAudits\Index::class)->middleware('permission:manage audits')->name('index');
        Route::get('/create', Admin\WarehouseAudits\Create::class)->name('create');
        Route::get('{warehouse_audit}/show', Admin\WarehouseAudits\Show::class)->name('show');
    });
});
