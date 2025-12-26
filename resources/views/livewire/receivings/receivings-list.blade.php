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
    
    <div class="mb-4">
        <flux:heading class="text-lg sm:text-xl">{{ __('Receivings for') }} {{ $delivery->identifier }}</flux:heading>
        <flux:subheading class="text-sm">{{ __('All receiving records for this delivery') }}</flux:subheading>
    </div>

    @if($delivery->receivings->isEmpty())
        <div class="rounded-lg border border-zinc-200 bg-white p-6 text-center dark:border-zinc-700 dark:bg-zinc-800 sm:p-12">
            <flux:heading size="md" class="mb-2">{{ __('No receivings') }}</flux:heading>
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No stock has been received for this delivery yet.') }}</flux:text>
        </div>
    @else
        <div class="space-y-3">
            @foreach($delivery->receivings as $receiving)
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <flux:heading size="sm" class="text-base">{{ __('Receiving') }} #{{ $receiving->receiving_sequence }}</flux:heading>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 sm:gap-4 sm:text-sm">
                                <span>{{ __('By') }}: {{ $receiving->receiver->name }}</span>
                                <span>{{ $receiving->receiving_date->format('M d, Y') }}</span>
                                <span>{{ __('Items') }}: {{ $receiving->receivedItems->count() }}</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <flux:button :href="route('receivings.show', $receiving)" wire:navigate variant="ghost" size="sm" class="flex-1 sm:flex-none">
                                {{ __('View') }}
                            </flux:button>
                            <flux:button wire:click="delete({{ $receiving->id }})" wire:confirm="{{ __('Are you sure you want to delete this receiving? This will remove all received items and may affect stock calculations. This action cannot be undone.') }}" variant="danger" size="sm" class="flex-1 sm:flex-none">
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-4">
        <flux:button :href="route('deliveries.show', $delivery)" wire:navigate variant="ghost" class="w-full sm:w-auto">
            {{ __('Back to Delivery') }}
        </flux:button>
    </div>
</section>
