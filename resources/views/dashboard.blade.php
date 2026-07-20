<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-neutral-900 dark:text-white">
            Tableau de bord
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <!-- Zone 1 : prochains matchs (3/4) + statistiques (1/4) -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
                <div class="lg:col-span-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                            Prochains matchs
                        </h3>
                        <a href="{{ route('pronostics.index') }}" class="text-sm font-medium text-neutral-700 hover:underline dark:text-neutral-300">
                            Tout voir
                        </a>
                    </div>

                    @if ($prochainsMatches->isEmpty())
                        <div class="mt-3 rounded-lg bg-white p-6 text-sm text-neutral-500 shadow-sm dark:bg-neutral-900 dark:text-neutral-400">
                            Aucun match à venir pour le moment.
                        </div>
                    @else
                        <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($prochainsMatches as $match)
                                @php($prono = $match->pronostics->first())
                                <a
                                    href="{{ route('pronostics.index') }}"
                                    class="block rounded-lg bg-white p-4 shadow-sm transition hover:shadow-md dark:bg-neutral-900"
                                >
                                    <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                        {{ $match->date_heure->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="mt-1 font-semibold text-neutral-900 dark:text-white">
                                        {{ $match->equipe1() }} vs {{ $match->equipe2() }}
                                    </p>
                                    <div class="mt-3">
                                        @if ($prono)
                                            <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-400">
                                                Pronostiqué
                                            </span>
                                        @elseif ($match->isVerrouille())
                                            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-400">
                                                Verrouillé
                                            </span>
                                        @else
                                            <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                                À pronostiquer
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-1">
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        Mes statistiques
                    </h3>

                    <div class="mt-3 space-y-3">
                        <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-neutral-900">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Points ({{ $phase->nom ?? '—' }})</p>
                            <p class="mt-1 text-2xl font-bold text-neutral-900 dark:text-white">{{ $mesPoints }}</p>
                        </div>

                        <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-neutral-900">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">Classement</p>
                            <p class="mt-1 text-2xl font-bold text-neutral-900 dark:text-white">
                                {{ $monRang ? '#'.$monRang : '—' }}
                                @if ($monRang)
                                    <span class="text-sm font-normal text-neutral-400">/ {{ $nombreJoueurs }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="rounded-lg bg-white p-4 shadow-sm dark:bg-neutral-900">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">À pronostiquer</p>
                            <p class="mt-1 text-2xl font-bold text-neutral-900 dark:text-white">
                                {{ $prochainsMatches->filter(fn ($match) => $match->pronostics->isEmpty())->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Zone 2 : autres éléments (50/50) -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        Derniers résultats
                    </h3>

                    <div class="mt-3 overflow-hidden rounded-lg bg-white shadow-sm dark:bg-neutral-900">
                        @forelse ($derniersResultats as $match)
                            @php($prono = $match->pronostics->first())
                            <div class="flex items-center justify-between border-b border-neutral-100 px-4 py-3 last:border-0 dark:border-neutral-800">
                                <div>
                                    <p class="text-sm font-medium text-neutral-900 dark:text-white">
                                        {{ $match->equipe1() }} vs {{ $match->equipe2() }}
                                    </p>
                                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                        {{ $match->date_heure->format('d/m/Y') }} — {{ $match->score_j1 }}-{{ $match->score_j2 }}
                                    </p>
                                </div>
                                <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                    {{ $prono?->points_obtenus ?? 0 }} pt(s)
                                </span>
                            </div>
                        @empty
                            <p class="px-4 py-6 text-sm text-neutral-500 dark:text-neutral-400">
                                Aucun résultat pour l'instant.
                            </p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-neutral-500 dark:text-neutral-400">
                        Classement — top 5
                    </h3>

                    <div class="mt-3 overflow-hidden rounded-lg bg-white shadow-sm dark:bg-neutral-900">
                        @forelse ($classement as $i => $entree)
                            <div class="flex items-center justify-between border-b border-neutral-100 px-4 py-3 last:border-0 dark:border-neutral-800">
                                <div class="flex items-center gap-3">
                                    <span class="w-5 text-sm font-semibold text-neutral-400">{{ $i + 1 }}</span>
                                    <span class="text-sm font-medium text-neutral-900 dark:text-white">
                                        {{ $entree['user']->pseudo }}
                                    </span>
                                </div>
                                <span class="text-sm font-semibold text-neutral-700 dark:text-neutral-300">
                                    {{ $entree['points'] }} pt(s)
                                </span>
                            </div>
                        @empty
                            <p class="px-4 py-6 text-sm text-neutral-500 dark:text-neutral-400">
                                Aucun classement disponible.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
