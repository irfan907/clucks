<section class="w-full">
    <div class="mb-4">
        <flux:heading class="text-lg sm:text-xl">{{ __('Create User') }}</flux:heading>
        <flux:subheading class="text-sm">{{ __('Add a new user to the system') }}</flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-4">
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus />
        <flux:error name="name" />

        <flux:input wire:model="email" :label="__('Email')" type="email" required />
        <flux:error name="email" />

        <flux:input wire:model="password" :label="__('Password')" type="password" required />
        <flux:error name="password" />

        <flux:input wire:model="password_confirmation" :label="__('Confirm Password')" type="password" required />
        <flux:error name="password_confirmation" />

        <div class="flex flex-col gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-700 sm:flex-row sm:items-center">
            <flux:button :href="route('users.index')" wire:navigate variant="ghost" class="w-full sm:w-auto">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="w-full sm:w-auto">{{ __('Create') }}</flux:button>
        </div>
    </form>
</section>

