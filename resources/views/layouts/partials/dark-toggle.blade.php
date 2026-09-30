{{-- Bascule clair / sombre. L'icône suit la classe .dark via les variantes CSS. --}}
<button
    type="button"
    @click="window.toggleColorMode($event)"
    class="rounded-full p-2 text-[color:var(--zone-header-fg)] opacity-80 transition hover:bg-[var(--zone-header-border)] hover:opacity-100"
    title="Basculer le mode clair / sombre"
>
    {{-- Lune : visible en mode clair (clic pour passer en sombre). --}}
    <svg class="size-5 dark:hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
    </svg>
    {{-- Soleil : visible en mode sombre (clic pour passer en clair). --}}
    <svg class="hidden size-5 dark:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
    </svg>
    <span class="sr-only">Basculer le mode sombre</span>
</button>
