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
            <flux:heading class="text-lg sm:text-xl">{{ __('All Deliveries') }}</flux:heading>
            <flux:subheading class="text-sm">{{ __('All submitted deliveries') }}</flux:subheading>
        </div>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-4">
            @if($statusFilter)
                <flux:button wire:click="clearFilter" variant="ghost" size="sm" class="w-full sm:w-auto">
                    {{ __('Clear Filter') }}
                </flux:button>
            @endif
            <flux:select 
                wire:model.live="statusFilter" 
                :label="__('Status')" 
                class="w-full sm:w-48"
            >
                <flux:select.option value="">{{ __('All') }}</flux:select.option>
                <flux:select.option value="submitted">{{ __('Submitted') }}</flux:select.option>
                <flux:select.option value="approved">{{ __('Approved') }}</flux:select.option>
                <flux:select.option value="delivered">{{ __('Delivered') }}</flux:select.option>
            </flux:select>
        </div>
    </div>

    @if($deliveries->isEmpty())
        <div class="rounded-lg border border-zinc-200 bg-white p-6 text-center dark:border-zinc-700 dark:bg-zinc-800 sm:p-12">
            <flux:heading size="md" class="mb-2">{{ __('No deliveries') }}</flux:heading>
            <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No deliveries match your filters.') }}</flux:text>
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
                        <div class="flex flex-wrap items-center gap-2">
                            @if($delivery->status === \App\DeliveryStatus::Submitted)
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">{{ __('Submitted') }}</span>
                                <flux:button :href="route('deliveries.edit', $delivery)" wire:navigate variant="ghost" size="sm" class="flex-1 sm:flex-none">
                                    {{ __('Edit') }}
                                </flux:button>
                                <flux:button wire:click="delete({{ $delivery->id }})" wire:confirm="{{ __('Are you sure you want to delete this delivery? This action cannot be undone.') }}" variant="danger" size="sm" class="flex-1 sm:flex-none">
                                    {{ __('Delete') }}
                                </flux:button>
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
