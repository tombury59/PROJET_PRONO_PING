@props(['route', 'label', 'activePattern' => null])

@php
    $isActive = $activePattern ? request()->routeIs($activePattern) : request()->routeIs($route);

    $classes = 'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors ' .
        ($isActive
            ? 'bg-surface-900 text-white dark:bg-white dark:text-surface-900'
            : 'text-surface-700 hover:bg-surface-100 dark:text-surface-300 dark:hover:bg-white/5');
@endphp

<a
    href="{{ route($route) }}"
    @click="mobileOpen = false"
    x-bind:class="! sidebarOpen && 'lg:w-10 lg:justify-center lg:px-0 lg:mx-auto'"
    {{ $attributes->merge(['class' => $classes]) }}
>
    {{ $slot }}
    <span x-show="sidebarOpen || mobileOpen" x-transition.opacity class="truncate">{{ $label }}</span>
</a>
