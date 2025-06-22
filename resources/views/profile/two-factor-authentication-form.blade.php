<x-action-section>
    <x-slot name="title">
        {{ __('المصادقة الثنائية') }}
    </x-slot>

    <x-slot name="description">
        {{ __('أضف أمانًا إضافيًا إلى حسابك باستخدام المصادقة الثنائية.') }}
    </x-slot>

    <x-slot name="content">
        <h3 class="h5 font-weight-bold">
            @if ($this->enabled)
                {{ __('لقد قمت بتمكين المصادقة الثنائية.') }}
            @else
                {{ __('لم تقم بتمكين المصادقة الثنائية.') }}
            @endif
        </h3>

        <p class="mt-3">
            {{ __('عند تمكين المصادقة الثنائية، سيُطلب منك رمز آمن وعشوائي أثناء المصادقة. يمكنك استرداد هذا الرمز من تطبيق Google Authenticator على هاتفك.') }}
        </p>

        @if ($this->enabled)
            @if ($showingQrCode)
                <p class="mt-3">
                    {{ __('تم تمكين المصادقة الثنائية الآن. امسح رمز الاستجابة السريعة التالي باستخدام تطبيق المصادقة على هاتفك.') }}
                </p>

                <div class="mt-3">
                    {!! $this->user->twoFactorQrCodeSvg() !!}
                </div>
            @endif

            @if ($showingRecoveryCodes)
                <p class="mt-3">
                    {{ __('قم بتخزين رموز الاسترداد هذه في مدير كلمات مرور آمن. يمكن استخدامها لاستعادة الوصول إلى حسابك إذا فقدت جهاز المصادقة الثنائية الخاص بك.') }}
                </p>

                <div class="bg-light rounded p-3">
                    @foreach (json_decode(decrypt($this->user->two_factor_recovery_codes), true) as $code)
                        <div>{{ $code }}</div>
                    @endforeach
                </div>
            @endif
        @endif

        <div class="mt-3">
            @if (! $this->enabled)
                <x-confirms-password wire:then="enableTwoFactorAuthentication">
                    <x-button type="button" wire:loading.attr="disabled">
                        {{ __('تمكين') }}
                    </x-button>
                </x-confirms-password>
            @else
                @if ($showingRecoveryCodes)
                    <x-confirms-password wire:then="regenerateRecoveryCodes">
                        <x-secondary-button class="me-3">
                            <div wire:loading wire:target="regenerateRecoveryCodes" class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>

                            {{ __('تجديد رموز الاسترداد') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @else
                    <x-confirms-password wire:then="showRecoveryCodes">
                        <x-secondary-button class="me-3">
                            <div wire:loading wire:target="showRecoveryCodes" class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>

                            {{ __('إظهار رموز الاسترداد') }}
                        </x-secondary-button>
                    </x-confirms-password>
                @endif

                <x-confirms-password wire:then="disableTwoFactorAuthentication">
                    <x-danger-button wire:loading.attr="disabled">
                        <div wire:loading wire:target="disableTwoFactorAuthentication" class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>

                        {{ __('تعطيل') }}
                    </x-danger-button>
                </x-confirms-password>
            @endif
        </div>
    </x-slot>
</x-action-section>
