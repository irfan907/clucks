<section class="w-full">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading class="text-lg sm:text-xl">{{ __('Products') }}</flux:heading>
            <flux:subheading class="text-sm">{{ __('Manage products by category') }}</flux:subheading>
        </div>
        <flux:button :href="route('products.create')" wire:navigate variant="primary" class="w-full sm:w-auto">
            {{ __('New Product') }}
        </flux:button>
    </div>

    @if($categories->isEmpty())
        <div class="rounded-lg border border-zinc-200 bg-white p-6 text-center dark:border-zinc-700 dark:bg-zinc-800 sm:p-12">
            <flux:heading size="md" class="mb-2">{{ __('No categories') }}</flux:heading>
            <flux:text class="mb-4 text-sm text-zinc-500 dark:text-zinc-400 sm:mb-6">{{ __('Create a category first to add products.') }}</flux:text>
            <flux:button :href="route('categories.create')" wire:navigate variant="primary" class="w-full sm:w-auto">
                {{ __('New Category') }}
            </flux:button>
        </div>
    @elseif(empty($productsForTable))
        <div class="rounded-lg border border-zinc-200 bg-white p-6 text-center dark:border-zinc-700 dark:bg-zinc-800 sm:p-12">
            <flux:heading size="md" class="mb-2">{{ __('No products') }}</flux:heading>
            <flux:text class="mb-4 text-sm text-zinc-500 dark:text-zinc-400 sm:mb-6">{{ __('No active products found.') }}</flux:text>
        </div>
    @else
        <div class="overflow-x-auto -mx-4 sm:mx-0">
            <div class="inline-block min-w-full align-middle">
                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Category') }}
                                </th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Product') }}
                                </th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider hidden sm:table-cell">
                                    {{ __('Unit') }}
                                </th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Stock') }}
                                </th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider hidden md:table-cell">
                                    {{ __('Min') }}
                                </th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Status') }}
                                </th>
                                <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($productsForTable as $row)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-2 py-2 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                            <div class="h-3 w-3 rounded flex-shrink-0" style="background-color: {{ $row['category']->colour }}"></div>
                                            <span class="text-xs text-zinc-900 dark:text-zinc-100 truncate max-w-[80px] sm:max-w-none">{{ $row['category']->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 py-2">
                                        <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100">{{ $row['product']->name }}</span>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap hidden sm:table-cell">
                                        <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $row['product']->unit }}</span>
                                    </td>
                                    <td class="px-2 py-2">
                                        <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($row['current_stock'], 2) }} <span class="text-zinc-500 dark:text-zinc-400 sm:hidden">{{ $row['product']->unit }}</span></span>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap hidden md:table-cell">
                                        @if($row['product']->minimum_quantity)
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ number_format($row['product']->minimum_quantity, 2) }}</span>
                                        @else
                                            <span class="text-xs text-zinc-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap">
                                        @if($row['is_low_stock'])
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">{{ __('Low') }}</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ __('OK') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap">
                                        <div class="flex items-center gap-1">
                                            <flux:button :href="route('products.edit', $row['product'])" wire:navigate variant="ghost" size="sm" class="!h-7 !px-2 text-xs">
                                                {{ __('Edit') }}
                                            </flux:button>
                                            <flux:button wire:click="delete({{ $row['product']->id }})" wire:confirm="{{ __('Are you sure you want to delete this product?') }}" variant="ghost" size="sm" class="!h-7 !px-2 text-xs text-red-600 dark:text-red-400">
                                                {{ __('Delete') }}
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</section>
