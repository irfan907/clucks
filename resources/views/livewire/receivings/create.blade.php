<section class="w-full">
    <div class="mb-4">
        <flux:heading class="text-lg sm:text-xl">{{ __('Receive Stock') }}</flux:heading>
        <flux:subheading class="text-sm">{{ $delivery->identifier }}</flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-4">
        <flux:input wire:model="receiving_date" :label="__('Receiving Date')" type="date" required />
        <flux:error name="receiving_date" />

        <flux:textarea wire:model="receiving_note" :label="__('Receiving Note')" rows="3" />
        <flux:error name="receiving_note" />

        <div>
            <flux:heading size="md" class="mb-3 text-base sm:text-lg">{{ __('Items') }}</flux:heading>
            <div class="space-y-3">
                @foreach($remainingQuantities as $item)
                    @php
                        $deliveryItem = $item['delivery_item'];
                        $ordered = $item['ordered_quantity'];
                        $received = $item['total_received'];
                        $remaining = $item['remaining_quantity'];
                    @endphp
                    <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
                        <div class="space-y-3">
                            <div>
                                <flux:heading size="sm" class="text-base">{{ $deliveryItem->product_name_snapshot }}</flux:heading>
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 sm:gap-4 sm:text-sm">
                                    <span>{{ __('Ordered') }}: <strong>{{ number_format($ordered, 2) }} {{ $deliveryItem->unit_snapshot }}</strong></span>
                                    <span>{{ __('Received') }}: <strong>{{ number_format($received, 2) }} {{ $deliveryItem->unit_snapshot }}</strong></span>
                                    <span>{{ __('Remaining') }}: <strong class="text-blue-600 dark:text-blue-400">{{ number_format($remaining, 2) }} {{ $deliveryItem->unit_snapshot }}</strong></span>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <flux:input 
                                    wire:model="items.{{ $deliveryItem->id }}.received_quantity" 
                                    :label="__('Received Quantity')" 
                                    type="number" 
                                    step="0.01" 
                                    min="0"
                                    required
                                />
                                <flux:error name="items.{{ $deliveryItem->id }}.received_quantity" />
                                <flux:input 
                                    wire:model="items.{{ $deliveryItem->id }}.item_note" 
                                    :label="__('Item Note')" 
                                    type="text"
                                    placeholder="{{ __('Optional note') }}"
                                />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-700 sm:flex-row sm:items-center sm:justify-end">
            <flux:button :href="route('receivings.select-delivery')" wire:navigate variant="ghost" class="w-full sm:w-auto">{{ __('Cancel') }}</flux:button>
            <flux:button type="submit" variant="primary" class="w-full sm:w-auto">{{ __('Save Receiving') }}</flux:button>
        </div>
    </form>
</section>
