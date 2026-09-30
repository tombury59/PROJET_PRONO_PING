<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        @include('layouts.partials.fonts')

        <!-- PWA -->
        @include('layouts.partials.pwa')

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Thème (couleur choisie par l'admin) -->
        @include('layouts.partials.theme')

        <!-- Mode sombre (avant rendu, anti-clignotement) -->
        @include('layouts.partials.color-mode')
    </head>
    <body class="font-sans antialiased">
        <div
            x-data="{
                mobileOpen: false,
                sidebarOpen: JSON.parse(localStorage.getItem('sidebar-open') ?? 'true'),
            }"
            x-effect="localStorage.setItem('sidebar-open', JSON.stringify(sidebarOpen))"
            class="relative flex h-svh w-full overflow-hidden bg-[var(--zone-page)]"
        >
            <!-- This allows screen readers to skip the sidebar and go directly to the main content. -->
            <a class="sr-only" href="#main-content">passer au contenu principal</a>

            <!-- dark overlay for when the sidebar is open on smaller screens -->
            <div
                x-cloak
                x-show="mobileOpen"
                x-transition.opacity
                @click="mobileOpen = false"
                class="fixed inset-0 z-20 bg-surface-950/10 backdrop-blur-xs lg:hidden"
                aria-hidden="true"
            ></div>

            @include('layouts.partials.sidebar')

            <div class="flex h-svh w-full flex-col overflow-y-auto">
                <!-- Mobile top bar -->
                <div class="flex shrink-0 items-center gap-3 border-b border-[color:var(--zone-header-border)] bg-[var(--zone-header)] px-4 py-3 text-[color:var(--zone-header-fg)] lg:hidden">
                    <button
                        @click="mobileOpen = true"
                        class="rounded-sm p-1.5 text-[color:var(--zone-header-fg)] hover:bg-[var(--zone-header-border)]"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                            <path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75Zm0 10.5a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5a.75.75 0 0 1-.75-.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">ouvrir le menu</span>
                    </button>
                    <span class="mr-auto font-semibold text-[color:var(--zone-header-fg)]">{{ config('app.name') }}</span>

                    @include('layouts.partials.dark-toggle')
                    @include('layouts.partials.notification-bell')
                </div>

                <header class="border-b border-[color:var(--zone-header-border)] bg-[var(--zone-header)] text-[color:var(--zone-header-fg)]">
                    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-6 sm:px-6 lg:px-8">
                        <div class="min-w-0 flex-1">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>

                        <div class="ml-4 hidden shrink-0 items-center gap-1 lg:flex">
                            @include('layouts.partials.dark-toggle')
                            @include('layouts.partials.notification-bell')
                        </div>
                    </div>
                </header>

                <main id="main-content" class="flex-1 px-4">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
