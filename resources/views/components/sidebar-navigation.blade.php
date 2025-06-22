<div class="sidebar-wrapper">
    <nav class="mt-2"> <!--begin::Sidebar Menu-->
        <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

            <li class="nav-header">التهيئة</li>
            <x-new-nav-link title="لوحة القيادة" bi_icon="bi-speedometer" route="admin.dashboard" />
            {{-- <x-new-nav-link title="Overview" bi_icon="bi-wallet" route="admin.accounts-summary" /> --}}
            @if (auth()->user()->hasPermission('manage roles'))
                <x-new-nav-link-dropdown title="الأدوار" bi_icon="bi-person-check" route="admin.roles*">
                    <x-new-nav-link title="قائمة الأدوار" bi_icon="" route="admin.roles.index" />
                    <x-new-nav-link title="إنشاء دور" bi_icon="" route="admin.roles.create" />
                </x-new-nav-link-dropdown>
            @endif
            @if (auth()->user()->hasPermission('manage warehouses'))
                <x-new-nav-link-dropdown title="المخازن" bi_icon="bi-building" route="admin.warehouses*">
                    <x-new-nav-link title="قائمة المخازن" bi_icon="" route="admin.warehouses.index" />
                    <x-new-nav-link title="إنشاء مخزن" bi_icon="" route="admin.warehouses.create" />
                </x-new-nav-link-dropdown>
            @endif
            @if (auth()->user()->hasPermission('manage users'))
                <x-new-nav-link-dropdown title="المستخدمون" bi_icon="bi-people" route="admin.users*">
                    <x-new-nav-link title="قائمة المستخدمين" bi_icon="" route="admin.users.index" />
                    <x-new-nav-link title="إنشاء مستخدم" bi_icon="" route="admin.users.create" />
                </x-new-nav-link-dropdown>
            @endif
            @if (auth()->user()->hasPermission('manage suppliers'))
            <li class="nav-header">إدارة علاقات العملاء</li>
                <x-new-nav-link-dropdown title="الموردون" bi_icon="bi-truck-flatbed" route="admin.suppliers*">
                    <x-new-nav-link title="قائمة الموردين" bi_icon="" route="admin.suppliers.index" />
                    <x-new-nav-link title="إنشاء مورد" bi_icon="" route="admin.suppliers.create" />
                </x-new-nav-link-dropdown>
                @endif

                <li class="nav-header">إدارة المنتجات</li>

            @if (auth()->user()->hasPermission('manage units'))
                <x-new-nav-link-dropdown title="الوحدات" bi_icon="bi-box" route="admin.units*">
                    <x-new-nav-link title="قائمة الوحدات" bi_icon="" route="admin.units.index" />
                    <x-new-nav-link title="إنشاء وحدة" bi_icon="" route="admin.units.create" />
                </x-new-nav-link-dropdown>
            @endif
            @if (auth()->user()->hasPermission('manage product categories'))
                <x-new-nav-link-dropdown title="فئات المنتجات" bi_icon="bi-boxes"
                    route="admin.product-categories*">
                    <x-new-nav-link title="قائمة الفئات" bi_icon="" route="admin.product-categories.index" />
                    <x-new-nav-link title="إنشاء فئة" bi_icon="" route="admin.product-categories.create" />
                </x-new-nav-link-dropdown>
            @endif
            @if (auth()->user()->hasPermission('manage products'))
                <x-new-nav-link-dropdown title="المنتجات" bi_icon="bi-box" route="admin.products*">
                    <x-new-nav-link title="قائمة المنتجات" bi_icon="" route="admin.products.index" />
                    <x-new-nav-link title="إنشاء منتج" bi_icon="" route="admin.products.create" />
                </x-new-nav-link-dropdown>
                
                <x-new-nav-link-dropdown title="منتجات المخزن" bi_icon="bi-box" route="admin.warehouse-products*">
                    <x-new-nav-link title="قائمة منتجات المخزن" bi_icon="" route="admin.warehouse-products.index" />
                    <x-new-nav-link title="إضافة منتج" bi_icon="" route="admin.warehouse-products.add" />
                </x-new-nav-link-dropdown>
            @endif
            @if (auth()->user()->hasPermission('view all warehouse products'))
                <x-new-nav-link title="منتجات كل المخازن" bi_icon="bi-card-list" route="admin.all-warehouse-products.index" />
            @endif
            <li class="nav-header">المخزون</li>

            @if (auth()->user()->hasPermission('manage inventory transfers'))
                <x-new-nav-link-dropdown title="نقل المخزون" bi_icon="bi-arrow-left-right" route="admin.inventory-transfers*">
                    <x-new-nav-link title="قائمة النقل" bi_icon="" route="admin.inventory-transfers.index" />
                    <x-new-nav-link title="إنشاء نقل" bi_icon="" route="admin.inventory-transfers.create" />
                    <x-new-nav-link title="طلبات النقل الخاصة بي" bi_icon="" route="admin.inventory-transfers.my-requests" />
                    <x-new-nav-link title="تأكيدات النقل المعلقة" bi_icon="" route="admin.inventory-transfers.pending-confirmations" />
                </x-new-nav-link-dropdown>
            @endif

            @if (auth()->user()->hasPermission('manage audits'))
                <x-new-nav-link-dropdown title="تدقيق المخزون" bi_icon="bi-clipboard-check" route="admin.warehouse-audits*">
                    <x-new-nav-link title="قائمة عمليات التدقيق" bi_icon="" route="admin.warehouse-audits.index" />
                    <x-new-nav-link title="إنشاء تدقيق جديد" bi_icon="" route="admin.warehouse-audits.create" />
                </x-new-nav-link-dropdown>
            @endif

            @if (auth()->user()->hasPermission('manage product deletion requests'))
                <x-new-nav-link-dropdown title="طلبات حذف المنتجات" bi_icon="bi-trash" route="admin.product-deletion-requests*">
                    <x-new-nav-link title="قائمة الطلبات" bi_icon="" route="admin.product-deletion-requests.index" />
                    <x-new-nav-link title="إنشاء طلب" bi_icon="" route="admin.product-deletion-requests.create" />
                </x-new-nav-link-dropdown>
            @endif

            <li class="nav-header">تحويل الكهنة</li>
            @if (auth()->user()->hasPermission('request obsolete conversion'))
                <x-new-nav-link title="طلب تحويل كهنة" bi_icon="bi-recycle" route="admin.obsolete-conversions.create" />
            @endif
            @if (auth()->user()->hasPermission('approve obsolete conversion requests'))
                <x-new-nav-link-dropdown title="مراجعة طلبات الكهنة" bi_icon="bi-check-circle" route="admin.obsolete-conversions*">
                    <x-new-nav-link title="قائمة الطلبات" bi_icon="" route="admin.obsolete-conversions.index" />
                </x-new-nav-link-dropdown>
            @endif
        </ul> <!--end::Sidebar Menu-->
    </nav>
</div>
