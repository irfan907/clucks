<section class="w-full">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading class="text-lg sm:text-xl">{{ __('Categories') }}</flux:heading>
            <flux:subheading class="text-sm">{{ __('Manage product categories') }}</flux:subheading>
        </div>
        <flux:button :href="route('categories.create')" wire:navigate variant="primary" class="w-full sm:w-auto">
            {{ __('New Category') }}
        </flux:button>
    </div>

    @if($categories->isEmpty())
        <div class="rounded-lg border border-zinc-200 bg-white p-6 text-center dark:border-zinc-700 dark:bg-zinc-800 sm:p-12">
            <flux:heading size="md" class="mb-2">{{ __('No categories') }}</flux:heading>
            <flux:text class="mb-4 text-sm text-zinc-500 dark:text-zinc-400 sm:mb-6">{{ __('Get started by creating your first category.') }}</flux:text>
            <flux:button :href="route('categories.create')" wire:navigate variant="primary" class="w-full sm:w-auto">
                {{ __('New Category') }}
            </flux:button>
        </div>
    @else
        <div class="space-y-3">
            @foreach($categories as $category)
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 flex-shrink-0 rounded-lg sm:h-12 sm:w-12" style="background-color: {{ $category->colour }}"></div>
                            <div class="min-w-0 flex-1">
                                <flux:heading size="sm" class="text-base">{{ $category->name }}</flux:heading>
                                @if($category->description)
                                    <flux:text class="mt-1 text-sm line-clamp-1">{{ $category->description }}</flux:text>
                                @endif
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 sm:gap-4 sm:text-sm">
                                    <span>{{ __('Order') }}: {{ $category->display_order }}</span>
                                    @if($category->is_active)
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ __('Active') }}</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300">{{ __('Inactive') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button :href="route('categories.edit', $category)" wire:navigate variant="ghost" size="sm" class="flex-1 sm:flex-none">
                                {{ __('Edit') }}
                            </flux:button>
                            <flux:button wire:click="delete({{ $category->id }})" wire:confirm="{{ __('Are you sure you want to delete this category?') }}" variant="ghost" size="sm" class="flex-1 text-red-600 dark:text-red-400 sm:flex-none">
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
