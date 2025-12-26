<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <a href="{{ route('dashboard') }}" class="ms-2 me-5 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navbar.item>
                <flux:navbar.item icon="tag" :href="route('categories.index')" :current="request()->routeIs('categories.*')" wire:navigate>
                    {{ __('Categories') }}
                </flux:navbar.item>
                <flux:navbar.item icon="cube" :href="route('products.index')" :current="request()->routeIs('products.*')" wire:navigate>
                    {{ __('Products') }}
                </flux:navbar.item>
                <flux:navbar.item icon="archive-box" :href="route('stock.index')" :current="request()->routeIs('stock.*')" wire:navigate>
                    {{ __('Stock') }}
                </flux:navbar.item>
                <flux:navbar.item icon="document-text" :href="route('deliveries.my')" :current="request()->routeIs('deliveries.*')" wire:navigate>
                    {{ __('Deliveries') }}
                </flux:navbar.item>
                <flux:navbar.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>
                    {{ __('Users') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 rtl:space-x-reverse py-0!">
                <flux:tooltip :content="__('Search')" position="bottom">
                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" :label="__('Search')" />
                </flux:tooltip>
            </flux:navbar>

            <!-- Desktop User Menu -->
            <flux:dropdown position="top" align="end">
                <flux:profile
                    class="cursor-pointer"
                    :initials="auth()->user()->initials()"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <!-- Mobile Menu -->
        <flux:sidebar stashable sticky class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('Platform')">
                    <flux:navlist.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                      {{ __('Dashboard') }}
                    </flux:navlist.item>
                </flux:navlist.group>
                <flux:navlist.group :heading="__('Stock Management')">
                    <flux:navlist.item icon="tag" :href="route('categories.index')" :current="request()->routeIs('categories.*')" wire:navigate>{{ __('Categories') }}</flux:navlist.item>
                    <flux:navlist.item icon="cube" :href="route('products.index')" :current="request()->routeIs('products.*')" wire:navigate>{{ __('Products') }}</flux:navlist.item>
                    <flux:navlist.item icon="archive-box" :href="route('stock.index')" :current="request()->routeIs('stock.*')" wire:navigate>{{ __('Current Stock') }}</flux:navlist.item>
                </flux:navlist.group>
                <flux:navlist.group :heading="__('Deliveries')">
                    <flux:navlist.item icon="document-text" :href="route('deliveries.my')" :current="request()->routeIs('deliveries.my') || request()->routeIs('deliveries.create')" wire:navigate>{{ __('My Deliveries') }}</flux:navlist.item>
                    <flux:navlist.item icon="document-duplicate" :href="route('deliveries.all')" :current="request()->routeIs('deliveries.all') || (request()->routeIs('deliveries.*') && !request()->routeIs('deliveries.my') && !request()->routeIs('deliveries.create'))" wire:navigate>{{ __('All Deliveries') }}</flux:navlist.item>
                </flux:navlist.group>
                <flux:navlist.group :heading="__('Receiving')">
                    <flux:navlist.item icon="arrow-down-tray" :href="route('receivings.select-delivery')" :current="request()->routeIs('receivings.*')" wire:navigate>{{ __('Receive Stock') }}</flux:navlist.item>
                </flux:navlist.group>
                <flux:navlist.group :heading="__('Administration')">
                    <flux:navlist.item icon="users" :href="route('users.index')" :current="request()->routeIs('users.*')" wire:navigate>{{ __('Users') }}</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />

        </flux:sidebar>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
