<div>
    <x-slot:header>فئات المنتجات</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>إنشاء فئة منتج جديدة</h5>
        </div>
        <div class="card-body">

            <div class="mb-3">
                <label for="name" class="form-label">اسم الفئة</label>
                <input wire:model.live='category.name' type="text" class="form-control" name="name" id="name"
                    aria-describedby="" placeholder="أدخل اسم الفئة" />
                @error('category.name')
                    <small id="" class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>



            <button onclick="confirm('هل أنت متأكد من رغبتك في إنشاء هذه الفئة؟')||event.stopImmediatePropagation()"
                wire:click='save' class="btn btn-dark text-inv-primary">حفظ</button>
        </div>
    </div>
</div>
