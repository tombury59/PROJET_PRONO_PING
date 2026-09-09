<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Notifications
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-4 sm:px-6 lg:px-8">
            <x-card class="p-4" x-data="pushToggle">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-medium text-surface-900 dark:text-white">Notifications sur cet appareil</p>
                        <p class="text-xs text-surface-500 dark:text-surface-400">
                            Reçois les rappels et résultats même quand l'app est fermée.
                        </p>
                    </div>

                    <template x-if="! supported">
                        <span class="text-xs text-surface-400">Non disponible sur ce navigateur.</span>
                    </template>

                    <template x-if="supported && permission === 'denied'">
                        <span class="text-xs text-danger-500">Notifications bloquées dans les réglages du navigateur.</span>
                    </template>

                    <template x-if="supported && permission !== 'denied'">
                        <button
                            type="button"
                            @click="toggle()"
                            x-bind:disabled="busy"
                            class="rounded-md px-3 py-2 text-sm font-medium"
                            x-bind:class="subscribed
                                ? 'border border-surface-300 text-surface-700 hover:bg-surface-100 dark:border-surface-700 dark:text-surface-300 dark:hover:bg-white/5'
                                : 'bg-surface-900 text-white hover:bg-surface-700 dark:bg-white dark:text-surface-900 dark:hover:bg-surface-200'"
                        >
                            <span x-show="! busy" x-text="subscribed ? 'Désactiver' : 'Activer les notifications'"></span>
                            <span x-show="busy">…</span>
                        </button>
                    </template>
                </div>
                <p x-cloak x-show="error" x-text="error" class="mt-2 text-xs text-danger-600 dark:text-danger-400"></p>
            </x-card>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-surface-900">
                @forelse ($notifications as $notification)
                    <div class="group flex items-center border-b border-surface-100 last:border-0 hover:bg-surface-50 dark:border-surface-800 dark:hover:bg-white/5">
                        <a
                            href="{{ $notification->data['url'] ?? '#' }}"
                            class="flex-1 px-4 py-3"
                        >
                            <p class="text-sm text-surface-900 dark:text-white">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            <p class="mt-1 text-xs text-surface-400">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </a>

                        <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="pr-4">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="rounded p-1.5 text-surface-300 hover:text-danger-600 group-hover:text-surface-400 dark:hover:text-danger-400"
                                title="Supprimer"
                            >
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span class="sr-only">Supprimer la notification</span>
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="px-4 py-8 text-center text-sm text-surface-500 dark:text-surface-400">
                        Aucune notification pour l'instant.
                    </p>
                @endforelse
            </div>
        </div>
    </div>

    @vite(['resources/js/push.js'])
</x-app-layout>
