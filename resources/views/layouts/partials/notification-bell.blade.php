@php($notificationsNonLues = auth()->user()->unreadNotifications()->count())

<x-dropdown align="right" width="w-80">
    <x-slot name="trigger">
        <button class="relative rounded-full p-2 text-neutral-500 hover:bg-black/5 hover:text-neutral-900 dark:text-neutral-400 dark:hover:bg-white/5 dark:hover:text-white">
            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            @if ($notificationsNonLues > 0)
                <span class="absolute right-1.5 top-1.5 flex size-2.5 rounded-full bg-red-500"></span>
            @endif
            <span class="sr-only">Notifications</span>
        </button>
    </x-slot>

    <x-slot name="content">
        <div class="max-h-80 overflow-y-auto">
            @forelse (auth()->user()->notifications()->latest()->limit(5)->get() as $notification)
                <a
                    href="{{ $notification->data['url'] ?? '#' }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-white/5 {{ $notification->read_at ? '' : 'font-medium' }}"
                >
                    {{ $notification->data['message'] ?? '' }}
                </a>
            @empty
                <p class="px-4 py-3 text-sm text-neutral-500 dark:text-neutral-400">
                    Aucune notification.
                </p>
            @endforelse
        </div>
        <div class="border-t border-neutral-100 dark:border-neutral-800">
            <a
                href="{{ route('notifications.index') }}"
                class="block px-4 py-2 text-center text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-neutral-300 dark:hover:bg-white/5"
            >
                Voir tout
            </a>
        </div>
    </x-slot>
</x-dropdown>
