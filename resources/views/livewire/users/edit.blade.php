<section class="w-full">
    <div class="mb-4">
        <flux:heading class="text-lg sm:text-xl">{{ __('Edit User') }}</flux:heading>
        <flux:subheading class="text-sm">{{ __('Update user details') }}</flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-4">
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus />
        <flux:error name="name" />

        <flux:input wire:model="email" :label="__('Email')" type="email" required />
        <flux:error name="email" />

        <div>
            <flux:input wire:model="password" :label="__('New Password')" type="password" />
            <flux:error name="password" />
            <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Leave blank to keep current password') }}</flux:text>
        </div>

        @if($password)
            <flux:input wire:model="password_confirmation" :label="__('Confirm New Password')" type="password" />
            <flux:error name="password_confirmation" />
        @endif

        <div>
            <flux:input wire:model="pin" :label="__('PIN (4 digits)')" type="text" maxlength="4" pattern="[0-9]{4}" inputmode="numeric" placeholder="0000" />
            <flux:error name="pin" />
            <flux:text class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ __('Optional: Enter a new 4-digit PIN to change it. Leave blank to keep current PIN.') }}</flux:text>
        </div>

        <div class="flex flex-col gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-700 sm:flex-row sm:items-center">
            <flux:button :href="route('users.index')" wire:navigate variant="ghost" class="w-full sm:w-auto">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="w-full sm:w-auto">{{ __('Save') }}</flux:button>
        </div>
    </form>
</section>

