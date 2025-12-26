<section class="w-full">
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading class="text-lg sm:text-xl">{{ __('Create Delivery') }} {{ $delivery->id }}</flux:heading>
            <flux:subheading class="text-sm">{{ $delivery->identifier }}</flux:subheading>
        </div>
        <div class="flex items-center gap-2">
            <x-action-message on="delivery-saved">
                {{ __('Saved') }}
            </x-action-message>
        </div>
    </div>

    @if($currentCategory)
        <form wire:submit="save" class="space-y-4">
            <flux:textarea wire:model="notes" :label="__('Delivery Notes')" rows="2" />
            <flux:error name="notes" />

            <div>
                <flux:error name="items" class="mb-3" />
            </div>
            
            <div class="rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-800 sm:p-4">
                <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="h-4 w-4 rounded sm:h-5 sm:w-5" style="background-color: {{ $currentCategory->colour }}"></div>
                        <flux:heading size="md" class="text-sm sm:text-base">{{ $currentCategory->name }}</flux:heading>
                    </div>
                    <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('Category') }} {{ $currentCategoryIndex }} {{ __('of') }} {{ $totalCategoriesCount }}
                    </flux:text>
                </div>

                @if(empty($productsForTable))
                    <div class="py-4 text-center">
                        <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 sm:text-sm">{{ __('No active products in this category') }}</flux:text>
                    </div>
                @else
                    <div class="overflow-x-auto -mx-2 sm:mx-0">
                        <div class="inline-block min-w-full align-middle">
                            <div class="overflow-hidden rounded border border-zinc-200 dark:border-zinc-700">
                                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                                        <tr>
                                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                                {{ __('Product') }}
                                            </th>
                                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider hidden sm:table-cell">
                                                {{ __('Unit') }}
                                            </th>
                                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider w-20 sm:w-24">
                                                {{ __('Current Stock') }}
                                            </th>
                                            <th scope="col" class="px-2 py-1.5 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                                {{ __('Note') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                                        @foreach($productsForTable as $row)
                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                                <td class="px-2 py-2">
                                                    <div class="flex flex-col">
                                                        <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100">{{ $row['product']->name }}</span>
                                                        <span class="text-xs text-zinc-500 dark:text-zinc-400 sm:hidden">{{ $row['product']->unit }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-2 py-2 whitespace-nowrap hidden sm:table-cell">
                                                    <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $row['product']->unit }}</span>
                                                </td>
                                                <td class="px-2 py-2">
                                                    <input 
                                                        type="number" 
                                                        step="0.01" 
                                                        min="0"
                                                        wire:model.blur="items.{{ $row['category_id'] }}.{{ $row['product_id'] }}.current_stock"
                                                        value="{{ $row['current_stock'] }}"
                                                        placeholder="0"
                                                        class="w-full px-1.5 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-1 focus:ring-accent focus:border-transparent"
                                                    />
                                                </td>
                                                <td class="px-2 py-2">
                                                    <input 
                                                        type="text" 
                                                        wire:model.blur="items.{{ $row['category_id'] }}.{{ $row['product_id'] }}.note"
                                                        placeholder="{{ __('Optional') }}"
                                                        class="w-full px-1.5 py-1 text-xs border border-zinc-300 dark:border-zinc-600 rounded bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 focus:ring-1 focus:ring-accent focus:border-transparent"
                                                    />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex flex-col gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-700 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <flux:button type="button" wire:click="save" variant="ghost" class="w-full sm:w-auto">{{ __('Save') }}</flux:button>
                    @if(!$isLastCategory)
                        <flux:button type="button" wire:click="saveAndNext" variant="ghost" class="w-full sm:w-auto">{{ __('Save & Next') }}</flux:button>
                    @endif
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    @if(!$isFirstCategory)
                        <flux:button type="button" wire:click="goToPreviousCategory" variant="ghost" class="w-full sm:w-auto">{{ __('Previous') }}</flux:button>
                    @endif
                    @if($isLastCategory)
                        <flux:button type="button" wire:click="submit" wire:confirm="{{ __('Submit this delivery for review?') }}" variant="primary" class="w-full sm:w-auto">
                            {{ __('Submit for Review') }}
                        </flux:button>
                    @endif
                </div>
            </div>
        </form>
    @else
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
            <flux:text>{{ __('No active categories found.') }}</flux:text>
        </div>
    @endif
</section>
