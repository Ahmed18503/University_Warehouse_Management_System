<div>
    <x-slot:header>المنتجات</x-slot:header>

    <div class="card">
        <div class="card-header bg-inv-secondary text-inv-primary border-0">
            <h5>تعديل هذا المنتج</h5>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="" class="form-label">فئة المنتج</label>
                        <select wire:model.live='product.product_category_id' class="form-select " name=""
                            id="">
                            <option selected>اختر فئة المنتج</option>
                            @foreach ($productCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('product.product_category_id')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="" class="form-label">السعر</label>
                        <input wire:model.live='product.price' type="number" min="0" step="0.1" required
                            class="form-control" name="price" id="price" aria-describedby="price"
                            placeholder="أدخل سعر المنتج" />
                        @error('product.price')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">الاسم</label>
                    <input wire:model.live='product.name' type="text" class="form-control" name="name"
                        id="name" aria-describedby="name" placeholder="أدخل اسم المنتج" />
                    @error('product.name')
                        <small id="" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">وصف المنتج</label>
                    <textarea wire:model.live='product.description' class="form-control" name="" id="" rows="3"></textarea>
                    @error('product.description')
                        <small id="" class="form-text text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="col-md-6 col-12">
                    <div class="mb-3">
                        <label for="" class="form-label">وحدة قياس المنتج</label>
                        <select wire:model.live='product.unit_id' class="form-select " name="" id="">
                            <option selected>اختر وحدة قياس المنتج</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('product.unit_id')
                            <small id="" class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <button onclick="confirm('هل أنت متأكد من رغبتك في تحديث هذا المنتج؟')||event.stopImmediatePropagation()"
                wire:click='save' class="btn btn-dark text-inv-primary">حفظ</button>
        </div>
    </div>
</div>
