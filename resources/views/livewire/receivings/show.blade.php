<section class="w-full">
    @if(session()->has('message'))
        <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
            <flux:text class="text-sm text-green-800 dark:text-green-200">{{ session('message') }}</flux:text>
        </div>
    @endif
    
    @if(session()->has('error'))
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
            <flux:text class="text-sm text-red-800 dark:text-red-200">{{ session('error') }}</flux:text>
        </div>
    @endif
    
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading class="text-lg sm:text-xl">{{ __('Receiving') }} #{{ $receiving->receiving_sequence }}</flux:heading>
            <flux:subheading class="text-sm">{{ $receiving->delivery->identifier }}</flux:subheading>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <flux:button wire:click="delete" wire:confirm="{{ __('Are you sure you want to delete this receiving? This will remove all received items and may affect stock calculations. This action cannot be undone.') }}" variant="danger" size="sm" class="flex-1 sm:flex-none">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </div>

    <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 sm:text-sm">{{ __('Delivery') }}</flux:text>
            <flux:heading size="sm" class="mt-2 text-base">{{ $receiving->delivery->identifier }}</flux:heading>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 sm:text-sm">{{ __('Received by') }}</flux:text>
            <flux:heading size="sm" class="mt-2 text-base">{{ $receiving->receiver->name }}</flux:heading>
        </div>
        <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 sm:text-sm">{{ __('Receiving Date') }}</flux:text>
            <flux:heading size="sm" class="mt-2 text-base">{{ $receiving->receiving_date->format('M d, Y') }}</flux:heading>
        </div>
    </div>

    @if($receiving->receiving_note)
        <div class="mb-4">
            <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
                <flux:heading size="sm" class="mb-2 text-base">{{ __('Receiving Note') }}</flux:heading>
                <flux:text class="text-sm">{{ $receiving->receiving_note }}</flux:text>
            </div>
        </div>
    @endif

    <div>
        <flux:heading size="md" class="mb-3 text-base sm:text-lg">{{ __('Received Items') }}</flux:heading>
        <div class="space-y-3">
            @foreach($receiving->receivedItems as $receivedItem)
                @php
                    $deliveryItem = $receivedItem->deliveryItem;
                @endphp
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
                    <div>
                        <flux:heading size="sm" class="text-base">{{ $deliveryItem->product_name_snapshot }}</flux:heading>
                        <div class="mt-2 text-sm sm:text-base">
                            <span class="text-zinc-500 dark:text-zinc-400">{{ __('Received') }}:</span>
                            <strong class="ml-2 text-lg text-zinc-900 dark:text-zinc-100">{{ number_format($receivedItem->received_quantity, 2) }} {{ $deliveryItem->unit_snapshot }}</strong>
                        </div>
                        @if($receivedItem->item_note)
                            <flux:text class="mt-2 text-xs italic sm:text-sm">{{ $receivedItem->item_note }}</flux:text>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4">
        <flux:button :href="route('deliveries.show', $receiving->delivery)" wire:navigate variant="ghost" class="w-full sm:w-auto">
            {{ __('Back to Delivery') }}
        </flux:button>
    </div>
</section>
