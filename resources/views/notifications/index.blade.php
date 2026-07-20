<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-neutral-900 dark:text-white">
            Notifications
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto space-y-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-neutral-900">
                @forelse ($notifications as $notification)
                    <a
                        href="{{ $notification->data['url'] ?? '#' }}"
                        class="block border-b border-neutral-100 px-4 py-3 last:border-0 hover:bg-neutral-50 dark:border-neutral-800 dark:hover:bg-white/5"
                    >
                        <p class="text-sm text-neutral-900 dark:text-white">
                            {{ $notification->data['message'] ?? '' }}
                        </p>
                        <p class="mt-1 text-xs text-neutral-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </a>
                @empty
                    <p class="px-4 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                        Aucune notification pour l'instant.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
