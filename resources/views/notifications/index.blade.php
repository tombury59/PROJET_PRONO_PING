<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Notifications
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-4 sm:px-6 lg:px-8">
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
</x-app-layout>
