<section class="w-full">
    <div class="mb-4">
        <flux:heading class="text-lg sm:text-xl">{{ __('Create Category') }}</flux:heading>
        <flux:subheading class="text-sm">{{ __('Add a new product category') }}</flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-4">
        <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus />
        <flux:error name="name" />

        <flux:textarea wire:model="description" :label="__('Description')" rows="3" />
        <flux:error name="description" />

        <flux:input wire:model="display_order" :label="__('Display Order')" type="number" min="0" required />
        <flux:error name="display_order" />

        <div>
            <flux:label>{{ __('Colour') }}</flux:label>
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <flux:input wire:model="colour" type="color" class="h-12 w-20 flex-shrink-0 sm:w-24" />
                <flux:input wire:model="colour" type="text" pattern="^#[0-9A-Fa-f]{6}$" placeholder="#3B82F6" class="flex-1 min-w-0" />
                <div class="h-12 w-12 flex-shrink-0 rounded-lg border border-zinc-300 dark:border-zinc-700" style="background-color: {{ $colour }}"></div>
            </div>
            <flux:error name="colour" />
        </div>

        <flux:checkbox wire:model="is_active" :label="__('Active')" />

        <div class="flex flex-col gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-700 sm:flex-row sm:items-center">
            <flux:button :href="route('categories.index')" wire:navigate variant="ghost" class="w-full sm:w-auto">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="w-full sm:w-auto">{{ __('Create') }}</flux:button>
        </div>
    </form>
</section>
