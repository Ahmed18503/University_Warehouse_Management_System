<x-action-section>
    <x-slot name="title">
        {{ __('حذف الحساب') }}
    </x-slot>

    <x-slot name="description">
        {{ __('حذف حسابك بشكل دائم.') }}
    </x-slot>

    <x-slot name="content">
        <div>
            {{ __('بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته بشكل دائم. قبل حذف حسابك، يرجى تنزيل أي بيانات أو معلومات ترغب في الاحتفاظ بها.') }}
        </div>

        <div class="mt-3">
            <x-danger-button wire:click="confirmUserDeletion" wire:loading.attr="disabled">
                {{ __('حذف الحساب') }}
            </x-danger-button>
        </div>

        <!-- Delete User Confirmation Modal -->
        <x-dialog-modal wire:model="confirmingUserDeletion">
            <x-slot name="title">
                {{ __('حذف الحساب') }}
            </x-slot>

            <x-slot name="content">
                {{ __('هل أنت متأكد أنك تريد حذف حسابك؟ بمجرد حذف حسابك، سيتم حذف جميع موارده وبياناته بشكل دائم. الرجاء إدخال كلمة المرور الخاصة بك لتأكيد رغبتك في حذف حسابك بشكل دائم.') }}

                <div class="mt-2 w-md-75" x-data="{}" x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                    <x-input type="password" class="{{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="{{ __('كلمة المرور') }}"
                                 x-ref="password"
                                 wire:model.defer="password"
                                 wire:keydown.enter="deleteUser" />

                    <x-input-error for="password" />
                </div>
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$toggle('confirmingUserDeletion')"
                                        wire:loading.attr="disabled">
                    {{ __('إلغاء') }}
                </x-secondary-button>

                <x-danger-button wire:click="deleteUser" wire:loading.attr="disabled">
                    <div wire:loading wire:target="deleteUser" class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>

                    {{ __('حذف الحساب') }}
                </x-danger-button>
            </x-slot>
        </x-dialog-modal>
    </x-slot>

</x-action-section>
