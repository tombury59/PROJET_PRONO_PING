@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-surface-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm dark:border-surface-700 dark:bg-surface-800 dark:text-white dark:placeholder-surface-500 dark:focus:border-primary-500 dark:focus:ring-primary-500']) }}>
