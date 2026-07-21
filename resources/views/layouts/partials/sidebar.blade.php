<aside
    x-cloak
    :class="[
        mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarOpen ? 'lg:w-64' : 'lg:w-20',
    ]"
    class="fixed inset-y-0 left-0 z-30 flex w-64 shrink-0 flex-col border-r border-neutral-200 bg-white transition-all duration-200 ease-in-out lg:relative dark:border-neutral-800 dark:bg-neutral-950"
>
    <!-- Logo / brand -->
    <div class="flex h-16 shrink-0 items-center gap-2 border-b border-neutral-200 px-4 dark:border-neutral-800">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 overflow-hidden">
            <x-application-logo class="h-8 w-8 shrink-0 fill-current text-neutral-900 dark:text-white" />
            <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate font-semibold text-neutral-900 dark:text-white">
                {{ config('app.name') }}
            </span>
        </a>

        <!-- Close button (mobile only) -->
        <button
            @click="mobileOpen = false"
            class="ml-auto rounded-sm p-1.5 text-neutral-500 hover:bg-black/5 hover:text-neutral-900 lg:hidden dark:text-neutral-400 dark:hover:bg-white/5 dark:hover:text-white"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
            </svg>
            <span class="sr-only">fermer le menu</span>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <a
            href="{{ route('dashboard') }}"
            @click="mobileOpen = false"
            x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5' }}"
        >
            <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate">Tableau de bord</span>
        </a>

        <a
            href="{{ route('pronostics.index') }}"
            @click="mobileOpen = false"
            x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('pronostics.*') ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5' }}"
        >
            <svg class="size-5 shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                <path d="M28.414,24l-3-3l2.293-2.293l-1.414-1.414l-2.236,2.236l-3.588-4.186L25,11.46V6h-5.46L16,10.13
                    L12.46,6H7v5.46l4.531,3.884l-3.588,4.186l-2.236-2.236l-1.414,1.414L6.586,21l-3,3L7,27.414l3-3l2.293,2.293l1.414-1.414
                    l-2.237-2.237L16,19.174l4.53,3.882l-2.237,2.237l1.414,1.414L22,24.414l3,3L28.414,24z M6.414,24L8,22.414L8.586,23L7,24.586
                    L6.414,24z M9,10.54V8h2.54l3.143,3.667l-1.85,2.159L9,10.54z M20.46,8H23v2.54L10.053,21.638l-0.69-0.69L20.46,8z M18.95,16.645
                    l3.688,4.302l-0.69,0.69l-4.411-3.781L18.95,16.645z M25,24.586L23.414,23L24,22.414L25.586,24L25,24.586z"/>
            </svg>
            <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate">Pronostics</span>
        </a>

        <a
            href="{{ route('classement.index') }}"
            @click="mobileOpen = false"
            x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('classement.*') ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5' }}"
        >
            <!-- <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V9m4 8V5m4 12v-6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z" />
            </svg> -->

            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M22,7H16.333V4a1,1,0,0,0-1-1H8.667a1,1,0,0,0-1,1v7H2a1,1,0,0,0-1,1v8a1,1,0,0,0,1,1H22a1,1,0,0,0,1-1V8A1,1,0,0,0,22,7ZM7.667,19H3V13H7.667Zm6.666,0H9.667V5h4.666ZM21,19H16.333V9H21Z"/></svg>
            <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate">Classement</span>
        </a>

        <a
            href="{{ route('calendrier.index') }}"
            @click="mobileOpen = false"
            x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('calendrier.*') ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5' }}"
        >
            <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate">Calendrier</span>
        </a>

        <a
            href="{{ route('bonus.index') }}"
            @click="mobileOpen = false"
            x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('bonus.*') ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5' }}"
        >
            <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate">Bonus</span>
        </a>

        <a
            href="{{ route('profile.edit') }}"
            @click="mobileOpen = false"
            x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.edit') ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5' }}"
        >
            <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate">Profil</span>
        </a>

        @if (auth()->user()?->isAdmin())
            <div class="mt-4 border-t border-neutral-200 pt-4 dark:border-neutral-800">
                <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="block px-3 pb-1 text-xs font-semibold uppercase tracking-wider text-neutral-400 dark:text-neutral-500">
                    Administration
                </span>

                <a
                    href="{{ route('admin.phases.index') }}"
                    @click="mobileOpen = false"
                    x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.phases.*') ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5' }}"
                >
                    <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M4 7h16M5 7h14v12a1 1 0 01-1 1H6a1 1 0 01-1-1V7z" />
                    </svg>
                    <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate">Phases</span>
                </a>

                <a
                    href="{{ route('admin.matches.index') }}"
                    @click="mobileOpen = false"
                    x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.matches.*') ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5' }}"
                >
                    <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate">Matchs</span>
                </a>

                <a
                    href="{{ route('admin.questions-bonus.index') }}"
                    @click="mobileOpen = false"
                    x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.questions-bonus.*') ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : 'text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5' }}"
                >
                    <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate">Questions bonus</span>
                </a>
            </div>
        @endif
    </nav>

    <!-- Collapse toggle (desktop only) -->
    <button
        @click="sidebarOpen = !sidebarOpen"
        x-bind:class="! sidebarOpen && 'lg:justify-center lg:px-0'"
        class="hidden shrink-0 items-center gap-3 border-t border-neutral-200 px-4 py-3 text-sm font-medium text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 lg:flex dark:border-neutral-800 dark:text-neutral-400 dark:hover:bg-white/5 dark:hover:text-white"
    >
        <svg
            class="size-5 shrink-0 transition-transform"
            :class="{ 'rotate-180': !sidebarOpen }"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
        </svg>
        <span x-show="sidebarOpen || mobileOpen" x-transition.opacity>Réduire</span>
    </button>

    <!-- User / logout -->
    <div class="shrink-0 border-t border-neutral-200 p-3 dark:border-neutral-800">
        <div class="flex items-center gap-3 overflow-hidden px-1 py-1" x-bind:class="! sidebarOpen && 'lg:justify-center'">
            <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-neutral-900 text-xs font-semibold text-white dark:bg-white dark:text-neutral-900">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->pseudo, 0, 1)) : '?' }}
            </div>
            <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate text-sm font-medium text-neutral-700 dark:text-neutral-300">
                {{ auth()->user()->pseudo ?? '' }}
            </span>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button
                type="submit"
                x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
                class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-neutral-700 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-white/5"
            >
                <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span x-show="sidebarOpen || mobileOpen" x-transition.opacity>Déconnexion</span>
            </button>
        </form>
    </div>
</aside>
