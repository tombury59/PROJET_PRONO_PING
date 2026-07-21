<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
                Phases
            </h2>

            <a
                href="{{ route('admin.phases.create') }}"
                class="rounded-sm bg-surface-900 px-4 py-2 text-sm font-medium text-white hover:bg-surface-700 dark:bg-white dark:text-surface-900 dark:hover:bg-surface-200"
            >
                Nouvelle phase
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-success-50 px-4 py-3 text-sm text-success-700 dark:bg-success-900/30 dark:text-success-400">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-danger-50 px-4 py-3 text-sm text-danger-700 dark:bg-danger-900/30 dark:text-danger-400">
                    {{ session('error') }}
                </div>
            @endif

            <x-responsive-table>
                <x-slot:table>
                    <thead class="bg-surface-50 dark:bg-surface-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Début</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Fin</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Matchs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Reset classement</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                        @forelse ($phases as $phase)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-surface-900 dark:text-white">{{ $phase->nom }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">{{ $phase->date_debut->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">{{ $phase->date_fin->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">{{ $phase->matches_count }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">
                                    @if ($phase->reset_classement)
                                        <span class="rounded-full bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-900/40 dark:text-success-400">Oui</span>
                                    @else
                                        <span class="rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">Non</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <a href="{{ route('admin.phases.edit', $phase) }}" class="font-medium text-surface-700 underline-offset-2 hover:underline dark:text-surface-300">
                                        Modifier
                                    </a>

                                    <form method="POST" action="{{ route('admin.phases.destroy', $phase) }}" class="ml-3 inline" onsubmit="return confirm('Supprimer cette phase ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-danger-600 underline-offset-2 hover:underline dark:text-danger-400">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-surface-500 dark:text-surface-400">
                                    Aucune phase pour l'instant.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-slot:table>

                <x-slot:cards>
                    @forelse ($phases as $phase)
                        <x-card class="p-4">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-medium text-surface-900 dark:text-white">{{ $phase->nom }}</p>
                                @if ($phase->reset_classement)
                                    <span class="shrink-0 rounded-full bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-900/40 dark:text-success-400">Reset classement</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
                                Du {{ $phase->date_debut->format('d/m/Y') }} au {{ $phase->date_fin->format('d/m/Y') }}
                            </p>

                            <div class="mt-3 flex items-center justify-between gap-2">
                                <span class="text-xs text-surface-500 dark:text-surface-400">
                                    {{ $phase->matches_count }} matchs
                                </span>
                            </div>

                            <div class="mt-3 flex items-center gap-3 border-t border-surface-100 pt-3 text-xs font-semibold uppercase tracking-widest dark:border-surface-800">
                                <a href="{{ route('admin.phases.edit', $phase) }}" class="text-surface-700 hover:text-surface-900 dark:text-surface-300 dark:hover:text-white">
                                    Modifier
                                </a>

                                <form method="POST" action="{{ route('admin.phases.destroy', $phase) }}" onsubmit="return confirm('Supprimer cette phase ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-danger-600 hover:text-danger-500 dark:text-danger-400">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </x-card>
                    @empty
                        <x-card class="p-6 text-center text-sm text-surface-500 dark:text-surface-400">
                            Aucune phase pour l'instant.
                        </x-card>
                    @endforelse
                </x-slot:cards>
            </x-responsive-table>
        </div>
    </div>
</x-app-layout>
