<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-neutral-900 dark:text-white">
                Calendrier — {{ $mois->translatedFormat('F Y') }}
            </h2>

            <div class="flex items-center gap-2">
                <a
                    href="{{ route('calendrier.index', ['mois' => $moisPrecedent]) }}"
                    class="rounded-md border border-neutral-300 p-2 text-neutral-600 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-white/5"
                >
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="sr-only">Mois précédent</span>
                </a>

                <a
                    href="{{ route('calendrier.index') }}"
                    class="rounded-md border border-neutral-300 px-3 py-2 text-sm font-medium text-neutral-600 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-white/5"
                >
                    Aujourd'hui
                </a>

                <a
                    href="{{ route('calendrier.index', ['mois' => $moisSuivant]) }}"
                    class="rounded-md border border-neutral-300 p-2 text-neutral-600 hover:bg-neutral-100 dark:border-neutral-700 dark:text-neutral-300 dark:hover:bg-white/5"
                >
                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="sr-only">Mois suivant</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-4 sm:px-6 lg:px-8">
            @if ($phasesVisibles->isNotEmpty())
                <div class="flex flex-wrap items-center gap-4">
                    @foreach ($phasesVisibles as $phase)
                        <div class="flex items-center gap-2 text-sm text-neutral-600 dark:text-neutral-300">
                            <span class="size-3 rounded-full border-2 {{ $couleursParPhase[$phase->id] }}"></span>
                            {{ $phase->nom }}
                        </div>
                    @endforeach
                </div>
            @endif

            <x-card class="overflow-hidden p-0">
                <div class="grid grid-cols-7 border-b border-neutral-200 text-center text-xs font-semibold uppercase tracking-wider text-neutral-500 dark:border-neutral-800 dark:text-neutral-400">
                    @foreach (['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $jourSemaine)
                        <div class="py-2">{{ $jourSemaine }}</div>
                    @endforeach
                </div>

                <div class="grid grid-cols-7">
                    @foreach ($jours as $jour)
                        @php($phaseDuJour = $phasesVisibles->first(fn ($phase) => $jour->between($phase->date_debut, $phase->date_fin)))
                        @php($matchesDuJour = $matchesParJour->get($jour->format('Y-m-d'), collect()))
                        @php($horsMois = ! $jour->isSameMonth($mois))
                        @php($estAujourdhui = $jour->isToday())

                        <div
                            class="relative min-h-[7rem] border-b border-r border-neutral-100 p-1.5 dark:border-neutral-800 {{ $horsMois ? 'bg-neutral-50 dark:bg-neutral-900/40' : '' }} {{ $phaseDuJour && ! $horsMois ? $couleursParPhase[$phaseDuJour->id] : '' }} {{ auth()->user()->isAdmin() ? 'group hover:bg-neutral-100 dark:hover:bg-white/5' : '' }}"
                        >
                            @if (auth()->user()->isAdmin())
                                <a
                                    href="{{ route('admin.matches.create', ['date' => $jour->format('Y-m-d')]) }}"
                                    class="absolute inset-0"
                                    aria-label="Créer un match le {{ $jour->format('d/m/Y') }}"
                                ></a>
                            @endif

                            <div class="relative flex items-center justify-between">
                                <span
                                    class="flex size-6 items-center justify-center rounded-full text-xs font-medium {{ $estAujourdhui ? 'bg-neutral-900 text-white dark:bg-white dark:text-neutral-900' : ($horsMois ? 'text-neutral-300 dark:text-neutral-700' : 'text-neutral-600 dark:text-neutral-300') }}"
                                >
                                    {{ $jour->day }}
                                </span>

                                @if (auth()->user()->isAdmin())
                                    <svg class="size-4 text-neutral-400 opacity-0 transition-opacity group-hover:opacity-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                @endif
                            </div>

                            <div class="relative mt-1 space-y-1">
                                @foreach ($matchesDuJour as $match)
                                    <a
                                        href="{{ route('pronostics.index') }}"
                                        class="relative block truncate rounded border-l-2 bg-white px-1 py-0.5 text-[11px] leading-tight shadow-sm hover:shadow dark:bg-neutral-900 {{ $match->resultat_saisi ? 'border-green-500' : ($match->isVerrouille() ? 'border-amber-500' : 'border-neutral-300 dark:border-neutral-700') }}"
                                        title="{{ $match->equipe1() }} vs {{ $match->equipe2() }} — {{ $match->date_heure->format('H:i') }}"
                                    >
                                        <span class="font-medium text-neutral-500 dark:text-neutral-400">{{ $match->date_heure->format('H:i') }}</span>
                                        <span class="text-neutral-900 dark:text-white">{{ $match->equipe1() }} vs {{ $match->equipe2() }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
