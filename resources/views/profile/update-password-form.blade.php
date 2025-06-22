<x-form-section submit="updatePassword">
    <x-slot name="title">
        {{ __('تحديث كلمة المرور') }}
    </x-slot>

    <x-slot name="description">
        {{ __('تأكد من أن حسابك يستخدم كلمة مرور طويلة وعشوائية للبقاء آمنًا.') }}
    </x-slot>

    <x-slot name="form">
        <div class="w-md-75">
            <div class="mb-3">
                <x-label for="current_password" value="{{ __('كلمة المرور الحالية') }}" />
                <x-input id="current_password" type="password" class="{{ $errors->has('current_password') ? 'is-invalid' : '' }}"
                             wire:model.defer="state.current_password" autocomplete="current-password" />
                <x-input-error for="current_password" />
            </div>

            <div class="mb-3">
                <x-label for="password" value="{{ __('كلمة المرور الجديدة') }}" />
                <x-input id="password" type="password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                             wire:model.defer="state.password" autocomplete="new-password" />
                <x-input-error for="password" />
            </div>

            <div class="mb-3">
                <x-label for="password_confirmation" value="{{ __('تأكيد كلمة المرور') }}" />
                <x-input id="password_confirmation" type="password" class="{{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                             wire:model.defer="state.password_confirmation" autocomplete="new-password" />
                <x-input-error for="password_confirmation" />
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-button>
            <div wire:loading class="spinner-border spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>

            {{ __('حفظ') }}
        </x-button>
    </x-slot>
</x-form-section>
