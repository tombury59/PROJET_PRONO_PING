<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
                Matchs
            </h2>

            <a
                href="{{ route('admin.matches.create') }}"
                class="rounded-sm bg-surface-900 px-4 py-2 text-sm font-medium text-white hover:bg-surface-700 dark:bg-white dark:text-surface-900 dark:hover:bg-surface-200"
            >
                Nouveau match
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

            @if ($phases->isNotEmpty())
                <form method="GET" action="{{ route('admin.matches.index') }}" class="max-w-xs">
                    <x-input-label for="phase_id" value="Filtrer par phase" />
                    <select
                        id="phase_id"
                        name="phase_id"
                        onchange="this.form.submit()"
                        class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                    >
                        @foreach ($phases as $phase)
                            <option value="{{ $phase->id }}" @selected($selectedPhaseId == $phase->id)>
                                {{ $phase->nom }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            <x-responsive-table>
                <x-slot:table>
                    <thead class="bg-surface-50 dark:bg-surface-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Match</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Résultat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Pronostics</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                        @forelse ($matches as $match)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-surface-900 dark:text-white">
                                    {{ $match->equipe1() }} vs {{ $match->equipe2() }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">
                                    {{ $match->date_heure->format('d/m/Y H:i') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">
                                    @if ($match->resultat_saisi)
                                        <span class="rounded-full bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-900/40 dark:text-success-400">
                                            {{ $match->score_j1 }} - {{ $match->score_j2 }}
                                        </span>
                                    @else
                                        <span class="rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">
                                            En attente
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">
                                    {{ $match->pronostics_count }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <a href="{{ route('admin.matches.edit', $match) }}" class="font-medium text-surface-700 underline-offset-2 hover:underline dark:text-surface-300">
                                        Modifier
                                    </a>

                                    <form method="POST" action="{{ route('admin.matches.destroy', $match) }}" class="ml-3 inline" onsubmit="return confirm('Supprimer ce match ?');">
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
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-surface-500 dark:text-surface-400">
                                    Aucun match pour cette phase.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-slot:table>

                <x-slot:cards>
                    @forelse ($matches as $match)
                        <x-card class="p-4">
                            <p class="text-sm font-medium text-surface-900 dark:text-white">
                                {{ $match->equipe1() }} vs {{ $match->equipe2() }}
                            </p>
                            <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
                                {{ $match->date_heure->format('d/m/Y H:i') }}
                            </p>

                            <div class="mt-3 flex items-center justify-between gap-2">
                                @if ($match->resultat_saisi)
                                    <span class="rounded-full bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-900/40 dark:text-success-400">
                                        {{ $match->score_j1 }} - {{ $match->score_j2 }}
                                    </span>
                                @else
                                    <span class="rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">
                                        En attente
                                    </span>
                                @endif

                                <span class="text-xs text-surface-500 dark:text-surface-400">
                                    {{ $match->pronostics_count }} pronostics
                                </span>
                            </div>

                            <div class="mt-3 flex items-center gap-3 border-t border-surface-100 pt-3 text-xs font-semibold uppercase tracking-widest dark:border-surface-800">
                                <a href="{{ route('admin.matches.edit', $match) }}" class="text-surface-700 hover:text-surface-900 dark:text-surface-300 dark:hover:text-white">
                                    Modifier
                                </a>

                                <form method="POST" action="{{ route('admin.matches.destroy', $match) }}" onsubmit="return confirm('Supprimer ce match ?');">
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
                            Aucun match pour cette phase.
                        </x-card>
                    @endforelse
                </x-slot:cards>
            </x-responsive-table>
        </div>
    </div>
</x-app-layout>
