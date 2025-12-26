<section class="w-full">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading class="text-lg sm:text-xl">{{ __('Users') }}</flux:heading>
            <flux:subheading class="text-sm">{{ __('Manage system users') }}</flux:subheading>
        </div>
        <flux:button :href="route('users.create')" wire:navigate variant="primary" class="w-full sm:w-auto">
            {{ __('New User') }}
        </flux:button>
    </div>

    @if($users->isEmpty())
        <div class="rounded-lg border border-zinc-200 bg-white p-6 text-center dark:border-zinc-700 dark:bg-zinc-800 sm:p-12">
            <flux:heading size="md" class="mb-2">{{ __('No users') }}</flux:heading>
            <flux:text class="mb-4 text-sm text-zinc-500 dark:text-zinc-400 sm:mb-6">{{ __('Get started by creating your first user.') }}</flux:text>
            <flux:button :href="route('users.create')" wire:navigate variant="primary" class="w-full sm:w-auto">
                {{ __('New User') }}
            </flux:button>
        </div>
    @else
        <div class="space-y-3">
            @foreach($users as $user)
                <div class="rounded-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-800 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-zinc-100 text-sm font-semibold text-zinc-700 dark:bg-zinc-700 dark:text-zinc-300 sm:h-12 sm:w-12">
                                {{ $user->initials() }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <flux:heading size="sm" class="text-base">{{ $user->name }}</flux:heading>
                                <flux:text class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $user->email }}</flux:text>
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 sm:gap-4 sm:text-sm">
                                    <span>{{ __('Created') }}: {{ $user->created_at->format('M d, Y') }}</span>
                                    @if($user->email_verified_at)
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">{{ __('Verified') }}</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">{{ __('Unverified') }}</span>
                                    @endif
                                    @if($user->id === auth()->id())
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">{{ __('You') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:button :href="route('users.edit', $user)" wire:navigate variant="ghost" size="sm" class="flex-1 sm:flex-none">
                                {{ __('Edit') }}
                            </flux:button>
                            @if($user->id !== auth()->id())
                                <flux:button wire:click="delete({{ $user->id }})" wire:confirm="{{ __('Are you sure you want to delete this user?') }}" variant="ghost" size="sm" class="flex-1 text-red-600 dark:text-red-400 sm:flex-none">
                                    {{ __('Delete') }}
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

