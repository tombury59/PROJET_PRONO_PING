<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-neutral-900 dark:text-white">
            Pronostics {{ $phase ? '— '.$phase->nom : '' }}
        </h2>
    </x-slot>

    <div class="py-12 ">
        <div class="mx-auto max-w-5xl space-y-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
                    {{ session('status') }}
                </div>
            @endif

            @if (! $phase)
                <div class="rounded-lg bg-white p-6 text-sm text-neutral-500 shadow-sm dark:bg-neutral-900 dark:text-neutral-400">
                    Aucune phase en cours pour le moment.
                </div>
            @elseif ($matches->isEmpty())
                <div class="rounded-lg bg-white p-6 text-sm text-neutral-500 shadow-sm dark:bg-neutral-900 dark:text-neutral-400">
                    Aucun match programmé pour cette phase.
                </div>
            @else
                @foreach ($matches as $match)
                    @php($prono = $match->pronostics->first())
                    @php($verrouille = $match->isVerrouille())

                    <x-card class="p-6">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="font-semibold text-neutral-900 dark:text-white">
                                    {{ $match->equipe1() }} vs {{ $match->equipe2() }}
                                </p>
                                <p class="text-sm text-neutral-500 dark:text-neutral-400">
                                    {{ $match->date_heure->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($match->resultat_saisi)
                                    <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-400">
                                        Résultat : {{ $match->score_j1 }} - {{ $match->score_j2 }}
                                    </span>
                                    @if ($prono)
                                        <span class="rounded-full bg-neutral-100 px-2 py-0.5 text-xs font-medium text-neutral-600 dark:bg-neutral-800 dark:text-neutral-400">
                                            {{ $prono->points_obtenus ?? 0 }} pt(s)
                                        </span>
                                    @endif
                                @elseif ($verrouille)
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-700 dark:bg-amber-900/40 dark:text-amber-400">
                                        Verrouillé
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if ($match->resultat_saisi)
                            <p class="mt-4 text-sm text-neutral-500 dark:text-neutral-400">
                                Résultat : {{ $match->score_j1 }} - {{ $match->score_j2 }}
                            </p>
                        @else
                            <form
                                method="POST"
                                action="{{ route('pronostics.store', $match) }}"
                                class="mt-4 flex flex-wrap items-end gap-3"
                            >
                                @csrf

                                <div>
                                    <x-input-label :for="'score_j1_'.$match->id" :value="$match->equipe1()" />
                                    <input
                                        id="score_j1_{{ $match->id }}"
                                        name="prono_score_j1"
                                        type="number"
                                        min="0"
                                        max="3"
                                        value="{{ old('prono_score_j1', $prono->prono_score_j1 ?? '') }}"
                                        @disabled($verrouille)
                                        class="mt-1 block w-20 rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-neutral-100 disabled:text-neutral-400 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:disabled:bg-neutral-900"
                                        required
                                    />
                                </div>

                                <span class="pb-2 text-neutral-400">—</span>

                                <div>
                                    <x-input-label :for="'score_j2_'.$match->id" :value="$match->equipe2()" />
                                    <input
                                        id="score_j2_{{ $match->id }}"
                                        name="prono_score_j2"
                                        type="number"
                                        min="0"
                                        max="3"
                                        value="{{ old('prono_score_j2', $prono->prono_score_j2 ?? '') }}"
                                        @disabled($verrouille)
                                        class="mt-1 block w-20 rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-neutral-100 disabled:text-neutral-400 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white dark:disabled:bg-neutral-900"
                                        required
                                    />
                                </div>

                                @unless ($verrouille)
                                    <x-primary-button>
                                        {{ $prono ? 'Modifier' : 'Valider' }}
                                    </x-primary-button>
                                @endunless
                            </form>
                        @endif
                        <x-input-error :messages="$errors->get('prono_score_j1')" class="mt-2" />
                        <x-input-error :messages="$errors->get('prono_score_j2')" class="mt-2" />
                    </x-card>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
