<section class="w-full">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading class="text-lg sm:text-xl">{{ __('My Deliveries') }}</flux:heading>
            <flux:subheading class="text-sm">{{ __('Your delivery requests') }}</flux:subheading>
        </div>
        <flux:button :href="route('deliveries.create')" wire:navigate variant="primary" class="w-full sm:w-auto">
            {{ __('New Delivery') }}
        </flux:button>
    </div>

    @if($draft)
        <div class="mb-4">
            <div class="rounded-lg border-2 border-blue-500 bg-white p-4 dark:bg-zinc-800 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0 flex-1">
                        <flux:heading size="sm" class="text-base">{{ $draft->identifier }}</flux:heading>
                        <flux:text class="mt-1 text-xs sm:text-sm">{{ __('Draft') }} • {{ $draft->created_at->format('M d, Y H:i') }}</flux:text>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">{{ __('Draft') }}</span>
                        <flux:button :href="route('deliveries.create')" wire:navigate variant="primary" size="sm" class="flex-1 sm:flex-none">
                            {{ __('Continue') }}
                        </flux:button>
                        <flux:button wire:click="deleteDraft({{ $draft->id }})" wire:confirm="{{ __('Are you sure you want to delete this draft?') }}" variant="ghost" size="sm" class="flex-1 text-red-600 dark:text-red-400 sm:flex-none">
                            {{ __('Delete') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($deliveries->isEmpty() && !$draft)
        <div class="rounded-lg border border-zinc-200 bg-white p-6 text-center dark:border-zinc-700 dark:bg-zinc-800 sm:p-12">
            <flux:heading size="md" class="mb-2">{{ __('No deliveries') }}</flux:heading>
            <flux:text class="mb-4 text-sm text-zinc-500 dark:text-zinc-400 sm:mb-6">{{ __('Create your first delivery to get started.') }}</flux:text>
            <flux:button :href="route('deliveries.create')" wire:navigate variant="primary" class="w-full sm:w-auto">
                {{ __('New Delivery') }}
            </flux:button>
        </div>
    @else
        <div class="space-y-3">
            @foreach($deliveries as $delivery)
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0 flex-1">
                            <flux:heading size="sm" class="text-base">{{ $delivery->identifier }}</flux:heading>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 sm:gap-4 sm:text-sm">
                                <span>{{ $delivery->created_at->format('M d, Y H:i') }}</span>
                                <span>{{ __('Items') }}: {{ $delivery->items->count() }}</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @if($delivery->status === \App\DeliveryStatus::Submitted)
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">{{ __('Submitted') }}</span>
                            @elseif($delivery->status === \App\DeliveryStatus::Approved)
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ __('Approved') }}</span>
                            @elseif($delivery->status === \App\DeliveryStatus::Delivered)
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">{{ __('Delivered') }}</span>
                            @endif
                            <flux:button :href="route('deliveries.show', $delivery)" wire:navigate variant="ghost" size="sm" class="flex-1 sm:flex-none">
                                {{ __('View') }}
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
