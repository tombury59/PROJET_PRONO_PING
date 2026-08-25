@props(['user'])

@php($url = $user->avatarUrl())

<span {{ $attributes->merge(['class' => 'inline-flex size-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-surface-200 text-xs font-semibold text-surface-600 dark:bg-surface-700 dark:text-surface-300']) }}>
    @if ($url)
        <img src="{{ $url }}" alt="{{ $user->pseudo }}" class="h-full w-full object-cover" />
    @else
        <span class="text-xs font-semibold text-surface-600 dark:text-surface-300">{{ $user->initiales() }}</span>
    @endif
</span>
