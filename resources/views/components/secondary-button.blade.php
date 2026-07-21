<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white border border-surface-300 rounded-md font-semibold text-xs text-surface-700 uppercase tracking-widest shadow-sm hover:bg-surface-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 dark:bg-surface-800 dark:border-surface-700 dark:text-surface-200 dark:hover:bg-surface-700']) }}>
    {{ $slot }}
</button>
