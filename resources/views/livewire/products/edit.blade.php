<section class="w-full">
    <div class="mb-4">
        <flux:heading class="text-lg sm:text-xl">{{ __('Edit Product') }}</flux:heading>
        <flux:subheading class="text-sm">{{ __('Update product details') }}</flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-4">
        <flux:select wire:model="category_id" :label="__('Category')" required>
            <flux:select.option value="">{{ __('Select a category') }}</flux:select.option>
            @foreach($categories as $category)
                <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
            @endforeach
        </flux:select>
        <flux:error name="category_id" />

        <flux:input wire:model="name" :label="__('Product Name')" type="text" required autofocus />
        <flux:error name="name" />

        <flux:input wire:model="unit" :label="__('Unit')" type="text" placeholder="kg, litre, piece, box, etc." required />
        <flux:error name="unit" />

        <flux:input wire:model="minimum_quantity" :label="__('Minimum Quantity')" type="number" step="0.01" min="0" />
        <flux:error name="minimum_quantity" />

        <flux:input wire:model="default_ordered_quantity" :label="__('Default Ordered Quantity (when stock is 0)')" type="number" step="0.01" min="0" />
        <flux:error name="default_ordered_quantity" />
        <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('This quantity will be automatically used when creating a delivery if the product stock is 0.') }}</flux:text>

        <flux:input wire:model="display_order" :label="__('Display Order')" type="number" min="0" required />
        <flux:error name="display_order" />

        <flux:checkbox wire:model="is_active" :label="__('Active')" />

        <div class="flex flex-col gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-700 sm:flex-row sm:items-center">
            <flux:button :href="route('products.index')" wire:navigate variant="ghost" class="w-full sm:w-auto">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="w-full sm:w-auto">{{ __('Save') }}</flux:button>
        </div>
    </form>
</section>
