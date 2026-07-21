<aside
    x-cloak
    :class="[
        mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarOpen ? 'lg:w-64' : 'lg:w-20',
    ]"
    class="fixed inset-y-0 left-0 z-30 flex w-64 shrink-0 flex-col border-r border-surface-200 bg-white transition-all duration-200 ease-in-out lg:relative dark:border-surface-800 dark:bg-surface-950"
>
    <!-- Logo / brand -->
    <div class="flex h-16 shrink-0 items-center gap-2 border-b border-surface-200 px-4 dark:border-surface-800">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 overflow-hidden">
            <x-application-logo class="h-8 w-8 shrink-0 fill-current text-surface-900 dark:text-white" />
            <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate font-semibold text-surface-900 dark:text-white">
                {{ config('app.name') }}
            </span>
        </a>

        <!-- Close button (mobile only) -->
        <button
            @click="mobileOpen = false"
            class="ml-auto rounded-sm p-1.5 text-surface-500 hover:bg-black/5 hover:text-surface-900 lg:hidden dark:text-surface-400 dark:hover:bg-white/5 dark:hover:text-white"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5" aria-hidden="true">
                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
            </svg>
            <span class="sr-only">fermer le menu</span>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <!-- Tableau de bord -->
        <x-sidebar-link route="dashboard" label="Tableau de bord">
            <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
        </x-sidebar-link>

        <!-- Pronostics -->
        <x-sidebar-link route="pronostics.index" active-pattern="pronostics.*" label="Pronostics">
            <svg class="size-5 shrink-0" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                <path d="M28.414,24l-3-3l2.293-2.293l-1.414-1.414l-2.236,2.236l-3.588-4.186L25,11.46V6h-5.46L16,10.13 L12.46,6H7v5.46l4.531,3.884l-3.588,4.186l-2.236-2.236l-1.414,1.414L6.586,21l-3,3L7,27.414l3-3l2.293,2.293l1.414-1.414 l-2.237-2.237L16,19.174l4.53,3.882l-2.237,2.237l1.414,1.414L22,24.414l3,3L28.414,24z M6.414,24L8,22.414L8.586,23L7,24.586 L6.414,24z M9,10.54V8h2.54l3.143,3.667l-1.85,2.159L9,10.54z M20.46,8H23v2.54L10.053,21.638l-0.69-0.69L20.46,8z M18.95,16.645 l3.688,4.302l-0.69,0.69l-4.411-3.781L18.95,16.645 z M25,24.586L23.414,23L24,22.414L25.586,24L25,24.586z"/>
            </svg>
        </x-sidebar-link>

        <!-- Classement -->
        <x-sidebar-link route="classement.index" active-pattern="classement.*" label="Classement">
            <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M22,7H16.333V4a1,1,0,0,0-1-1H8.667a1,1,0,0,0-1,1v7H2a1,1,0,0,0-1,1v8a1,1,0,0,0,1,1H22a1,1,0,0,0,1-1V8A1,1,0,0,0,22,7ZM7.667,19H3V13H7.667Zm6.666,0H9.667V5h4.666ZM21,19H16.333V9H21Z"/></svg>
        </x-sidebar-link>

        <!-- Calendrier -->
        <x-sidebar-link route="calendrier.index" active-pattern="calendrier.*" label="Calendrier">
            <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </x-sidebar-link>

        <!-- Bonus -->
        <x-sidebar-link route="bonus.index" active-pattern="bonus.*" label="Bonus">
            <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </x-sidebar-link>

        <!-- Profil -->
        <x-sidebar-link route="profile.edit" label="Profil">
            <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </x-sidebar-link>

        <!-- Administration -->
        @if (auth()->user()?->isAdmin())
            <div class="mt-4 border-t border-surface-200 pt-4 dark:border-surface-800">
                <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="block px-3 pb-1 text-xs font-semibold uppercase tracking-wider text-surface-400 dark:text-surface-500">
                    Administration
                </span>

                <div class="space-y-1 mt-1">
                    <x-sidebar-link route="admin.phases.index" active-pattern="admin.phases.*" label="Phases">
                        <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M4 7h16M5 7h14v12a1 1 0 01-1 1H6a1 1 0 01-1-1V7z" />
                        </svg>
                    </x-sidebar-link>

                    <x-sidebar-link route="admin.matches.index" active-pattern="admin.matches.*" label="Matchs">
                        <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-sidebar-link>

                    <x-sidebar-link route="admin.questions-bonus.index" active-pattern="admin.questions-bonus.*" label="Questions bonus">
                        <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </x-sidebar-link>

                    <x-sidebar-link route="admin.users.index" active-pattern="admin.users.*" label="Utilisateurs">
                        <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M14 12.25C13.2583 12.25 12.5333 12.0301 11.9166 11.618C11.2999 11.206 10.8193 10.6203 10.5355 9.93506C10.2516 9.24984 10.1774 8.49584 10.3221 7.76841C10.4668 7.04098 10.8239 6.3728 11.3484 5.84835C11.8728 5.3239 12.541 4.96675 13.2684 4.82206C13.9958 4.67736 14.7498 4.75162 15.4351 5.03545C16.1203 5.31928 16.706 5.79993 17.118 6.41661C17.5301 7.0333 17.75 7.75832 17.75 8.5C17.75 9.49456 17.3549 10.4484 16.6517 11.1517C15.9484 11.8549 14.9946 12.25 14 12.25ZM14 6.25C13.555 6.25 13.12 6.38196 12.75 6.62919C12.38 6.87643 12.0916 7.22783 11.9213 7.63896C11.751 8.0501 11.7064 8.5025 11.7932 8.93895C11.8801 9.37541 12.0943 9.77632 12.409 10.091C12.7237 10.4057 13.1246 10.62 13.561 10.7068C13.9975 10.7936 14.4499 10.749 14.861 10.5787C15.2722 10.4084 15.6236 10.12 15.8708 9.75003C16.118 9.38002 16.25 8.94501 16.25 8.5C16.25 7.90326 16.0129 7.33097 15.591 6.90901C15.169 6.48705 14.5967 6.25 14 6.25Z" />
                            <path d="M21 19.25C20.8019 19.2474 20.6126 19.1676 20.4725 19.0275C20.3324 18.8874 20.2526 18.6981 20.25 18.5C20.25 16.55 19.19 15.25 14 15.25C8.81 15.25 7.75 16.55 7.75 18.5C7.75 18.6989 7.67098 18.8897 7.53033 19.0303C7.38968 19.171 7.19891 19.25 7 19.25C6.80109 19.25 6.61032 19.171 6.46967 19.0303C6.32902 18.8897 6.25 18.6989 6.25 18.5C6.25 13.75 11.68 13.75 14 13.75C16.32 13.75 21.75 13.75 21.75 18.5C21.7474 18.6981 21.6676 18.8874 21.5275 19.0275C21.3874 19.1676 21.1981 19.2474 21 19.25Z"/>
                            <path d="M8.31999 13.06H7.99999C7.20434 12.9831 6.47184 12.5933 5.96361 11.9763C5.45539 11.3593 5.21308 10.5657 5.28999 9.77001C5.36691 8.97436 5.75674 8.24186 6.37373 7.73363C6.99073 7.22541 7.78434 6.9831 8.57999 7.06001C8.68201 7.0644 8.78206 7.08957 8.87401 7.13399C8.96596 7.1784 9.04787 7.24113 9.11472 7.31831C9.18157 7.3955 9.23196 7.48553 9.26279 7.58288C9.29362 7.68023 9.30425 7.78285 9.29402 7.88445C9.28379 7.98605 9.25292 8.08449 9.20331 8.17374C9.15369 8.26299 9.08637 8.34116 9.00548 8.40348C8.92458 8.46579 8.83181 8.51093 8.73286 8.53613C8.6339 8.56133 8.53084 8.56605 8.42999 8.55001C8.23479 8.53055 8.03766 8.55062 7.85038 8.60904C7.6631 8.66746 7.48952 8.76302 7.33999 8.89001C7.18812 9.01252 7.06216 9.16403 6.96945 9.33572C6.87673 9.50741 6.81913 9.69583 6.79999 9.89001C6.77932 10.0866 6.79797 10.2854 6.85488 10.4747C6.91178 10.6641 7.0058 10.8402 7.13144 10.9928C7.25709 11.1455 7.41186 11.2716 7.58673 11.3638C7.76159 11.456 7.95307 11.5125 8.14999 11.53C8.47553 11.5579 8.80144 11.4808 9.07999 11.31C9.24973 11.2053 9.45413 11.1722 9.64824 11.2182C9.84234 11.2641 10.0102 11.3853 10.115 11.555C10.2198 11.7248 10.2528 11.9292 10.2069 12.1233C10.1609 12.3174 10.0397 12.4853 9.86999 12.59C9.40619 12.8858 8.86998 13.0484 8.31999 13.06Z"/>
                            <path d="M3 18.5C2.80189 18.4974 2.61263 18.4176 2.47253 18.2775C2.33244 18.1374 2.25259 17.9481 2.25 17.75C2.25 15.05 2.97 13.25 6.5 13.25C6.69891 13.25 6.88968 13.329 7.03033 13.4697C7.17098 13.6103 7.25 13.8011 7.25 14C7.25 14.1989 7.17098 14.3897 7.03033 14.5303C6.88968 14.671 6.69891 14.75 6.5 14.75C4.15 14.75 3.75 15.5 3.75 17.75C3.74741 17.9481 3.66756 18.1374 3.52747 18.2775C3.38737 18.4176 3.19811 18.4974 3 18.5Z"/>
                        </svg>

                    </x-sidebar-link>
                </div>
            </div>
        @endif
    </nav>

    <!-- Collapse toggle (desktop only) -->
    <button
        @click="sidebarOpen = !sidebarOpen"
        x-bind:class="! sidebarOpen && 'lg:justify-center lg:px-0'"
        class="hidden shrink-0 items-center gap-3 border-t border-surface-200 px-4 py-3 text-sm font-medium text-surface-500 hover:bg-surface-100 hover:text-surface-900 lg:flex dark:border-surface-800 dark:text-surface-400 dark:hover:bg-white/5 dark:hover:text-white"
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
    <div class="shrink-0 border-t border-surface-200 p-3 dark:border-surface-800">
        <div class="flex items-center gap-3 overflow-hidden px-1 py-1" x-bind:class="! sidebarOpen && 'lg:justify-center'">
            <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-surface-900 text-xs font-semibold text-white dark:bg-white dark:text-surface-900">
                {{ auth()->check() ? strtoupper(substr(auth()->user()->pseudo, 0, 1)) : '?' }}
            </div>
            <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate text-sm font-medium text-surface-700 dark:text-surface-300">
                {{ auth()->user()->pseudo ?? '' }}
            </span>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="mt-1">
            @csrf
            <button
                type="submit"
                x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
                class="flex w-full items-center gap-3 rounded-md px-3 py-2 text-sm font-medium text-surface-700 hover:bg-surface-100 dark:text-surface-300 dark:hover:bg-white/5"
            >
                <svg class="size-5 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span x-show="sidebarOpen || mobileOpen" x-transition.opacity>Déconnexion</span>
            </button>
        </form>
    </div>
</aside>
