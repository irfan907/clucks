<section class="w-full">
    <div class="mb-4">
        <flux:heading class="text-lg sm:text-xl">{{ __('Select Delivery to Receive') }}</flux:heading>
        <flux:subheading class="text-sm">{{ __('Choose an approved delivery to record stock receiving') }}</flux:subheading>
    </div>

    <div class="mb-4">
        <flux:input wire:model.live.debounce.300ms="search" :label="__('Search by Identifier')" type="text" placeholder="{{ __('Search deliveries...') }}" />
    </div>

    @if($deliveries->isEmpty())
        <div class="rounded-lg border border-zinc-200 bg-white p-6 text-center dark:border-zinc-700 dark:bg-zinc-800 sm:p-12">
            <flux:heading size="md" class="mb-2">{{ __('No approved deliveries') }}</flux:heading>
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No approved deliveries available for receiving.') }}</flux:text>
        </div>
    @else
        <div class="space-y-3">
            @foreach($deliveries as $delivery)
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <flux:heading size="sm" class="text-base">{{ $delivery->identifier }}</flux:heading>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 sm:gap-4 sm:text-sm">
                                <span>{{ __('By') }}: {{ $delivery->creator->name }}</span>
                                <span>{{ $delivery->created_at->format('M d, Y H:i') }}</span>
                                <span>{{ __('Items') }}: {{ $delivery->items->count() }}</span>
                            </div>
                        </div>
                        <flux:button :href="route('receivings.create', $delivery)" wire:navigate variant="primary" size="sm" class="w-full sm:w-auto">
                            {{ __('Receive') }}
                        </flux:button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
