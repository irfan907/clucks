<section class="w-full">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading class="text-lg sm:text-xl">{{ $delivery->identifier }}</flux:heading>
            <flux:subheading class="text-sm">{{ __('Delivery details') }}</flux:subheading>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if($delivery->status === \App\DeliveryStatus::Submitted)
                <flux:button wire:click="approve" wire:confirm="{{ __('Approve this delivery?') }}" variant="primary" size="sm" class="flex-1 sm:flex-none">
                    {{ __('Approve') }}
                </flux:button>
            @elseif($delivery->status === \App\DeliveryStatus::Approved)
                <flux:button wire:click="finalize" wire:confirm="{{ __('Mark as delivered?') }}" variant="primary" size="sm" class="flex-1 sm:flex-none">
                    {{ __('Mark Delivered') }}
                </flux:button>
                <flux:button :href="route('receivings.create', $delivery)" wire:navigate variant="primary" size="sm" class="flex-1 sm:flex-none">
                    {{ __('Receive') }}
                </flux:button>
            @endif
            @if($delivery->status === \App\DeliveryStatus::Submitted)
                <flux:button :href="route('deliveries.edit', $delivery)" wire:navigate variant="ghost" size="sm" class="flex-1 sm:flex-none">
                    {{ __('Edit') }}
                </flux:button>
            @endif
        </div>
    </div>

    <!-- show error message if there is an error -->
    @if($errors->has('remainingQuantities'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/30 text-red-700 dark:text-red-300">
            <flux:text>{{ $errors->first('remainingQuantities') }}</flux:text>
        </div>
    @endif

    <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 sm:text-sm">{{ __('Status') }}</flux:text>
            <div class="mt-2">
                @if($delivery->status === \App\DeliveryStatus::Submitted)
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">{{ __('Submitted') }}</span>
                @elseif($delivery->status === \App\DeliveryStatus::Approved)
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ __('Approved') }}</span>
                @elseif($delivery->status === \App\DeliveryStatus::Delivered)
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">{{ __('Delivered') }}</span>
                @endif
            </div>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 sm:text-sm">{{ __('Created by') }}</flux:text>
            <flux:heading size="sm" class="mt-2 text-base">{{ $delivery->creator->name }}</flux:heading>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 sm:text-sm">{{ __('Created') }}</flux:text>
            <flux:heading size="sm" class="mt-2 text-base">{{ $delivery->created_at->format('M d, Y H:i') }}</flux:heading>
        </div>
    </div>

    @if($delivery->notes)
        <div class="mb-4">
            <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
                <flux:heading size="sm" class="mb-2 text-base">{{ __('Notes') }}</flux:heading>
                <flux:text class="text-sm">{{ $delivery->notes }}</flux:text>
            </div>
        </div>
    @endif

    <div class="mb-4">
        <flux:heading size="md" class="mb-3 text-base sm:text-lg">{{ __('Items') }}</flux:heading>
        @if($remainingQuantities->isEmpty())
            <div class="rounded-lg border border-zinc-200 bg-white p-4 text-center dark:border-zinc-700 dark:bg-zinc-800">
                <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 sm:text-sm">{{ __('No items in this delivery.') }}</flux:text>
            </div>
        @else
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-800">
                                <tr>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Product') }}
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider hidden sm:table-cell">
                                        {{ __('Unit') }}
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Ordered') }}
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Received') }}
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Remaining') }}
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider hidden md:table-cell">
                                        {{ __('Progress') }}
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Note') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($remainingQuantities as $item)
                                    @php
                                        $deliveryItem = $item['delivery_item'];
                                        $ordered = $item['ordered_quantity'];
                                        $received = $item['total_received'];
                                        $remaining = $item['remaining_quantity'];
                                        $progressPercent = $ordered > 0 ? min(100, ($received / $ordered) * 100) : 0;
                                    @endphp
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td class="px-2 py-2">
                                            <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100">{{ $deliveryItem->product_name_snapshot }}</span>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap hidden sm:table-cell">
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $deliveryItem->unit_snapshot }}</span>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap">
                                            <span class="text-xs text-zinc-900 dark:text-zinc-100">{{ number_format($ordered, 2) }} <span class="text-zinc-500 dark:text-zinc-400 sm:hidden">{{ $deliveryItem->unit_snapshot }}</span></span>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap">
                                            <span class="text-xs text-zinc-900 dark:text-zinc-100">{{ number_format($received, 2) }}</span>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap">
                                            <span class="text-xs font-semibold {{ $remaining > 0 ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100' }}">{{ number_format($remaining, 2) }}</span>
                                        </td>
                                        <td class="px-2 py-2 hidden md:table-cell">
                                            <div class="w-16">
                                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-700">
                                                    <div class="h-full bg-blue-500" style="width: {{ $progressPercent }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-2 py-2">
                                            @if($deliveryItem->item_note)
                                                <span class="text-xs italic text-zinc-500 dark:text-zinc-400" title="{{ $deliveryItem->item_note }}">{{ Str::limit($deliveryItem->item_note, 30) }}</span>
                                            @else
                                                <span class="text-xs text-zinc-400">-</span>
                                            @endif
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

    @if($delivery->receivings->isNotEmpty())
        <div>
            <flux:heading size="md" class="mb-3 text-base sm:text-lg">{{ __('Receivings') }}</flux:heading>
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-700">
                            <thead class="bg-zinc-50 dark:bg-zinc-800">
                                <tr>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Receiving') }}
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Received By') }}
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Date') }}
                                    </th>
                                    <th scope="col" class="px-2 py-2 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                                        {{ __('Actions') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-200 dark:divide-zinc-700">
                                @foreach($delivery->receivings as $receiving)
                                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                        <td class="px-2 py-2">
                                            <span class="text-xs font-medium text-zinc-900 dark:text-zinc-100">#{{ $receiving->receiving_sequence }}</span>
                                        </td>
                                        <td class="px-2 py-2">
                                            <span class="text-xs text-zinc-900 dark:text-zinc-100">{{ $receiving->receiver->name }}</span>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap">
                                            <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ $receiving->receiving_date->format('M d, Y') }}</span>
                                        </td>
                                        <td class="px-2 py-2 whitespace-nowrap">
                                            <flux:button :href="route('receivings.show', $receiving)" wire:navigate variant="ghost" size="sm" class="!h-7 !px-2 text-xs">
                                                {{ __('View') }}
                                            </flux:button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
