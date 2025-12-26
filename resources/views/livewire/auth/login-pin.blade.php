<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Log in with PIN')" :description="__('Enter your 4-digit PIN to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.pin.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- PIN -->
            <div>
                <flux:input
                    name="pin"
                    :label="__('PIN')"
                    type="text"
                    required
                    autofocus
                    maxlength="4"
                    pattern="[0-9]{4}"
                    inputmode="numeric"
                    placeholder="0000"
                    class="text-center text-2xl tracking-widest"
                />
                <flux:error name="pin" />
                <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Enter your 4-digit PIN') }}</flux:text>
            </div>

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-pin-button">
                    {{ __('Log in with PIN') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Or') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('log in with email and password') }}</flux:link>
        </div>
    </div>
</x-layouts.auth>

