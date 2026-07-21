<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Pronostics {{ $phase ? '— '.$phase->nom : '' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-8 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-success-50 px-4 py-3 text-sm text-success-700 dark:bg-success-900/30 dark:text-success-400">
                    {{ session('status') }}
                </div>
            @endif

            @if (! $phase)
                <div class="rounded-lg bg-white p-6 text-sm text-surface-500 shadow-sm dark:bg-surface-900 dark:text-surface-400">
                    Aucune phase en cours pour le moment.
                </div>
            @elseif ($matches->isEmpty())
                <div class="rounded-lg bg-white p-6 text-sm text-surface-500 shadow-sm dark:bg-surface-900 dark:text-surface-400">
                    Aucun match programmé pour cette phase.
                </div>
            @else
                @if ($matchesAFaire->isNotEmpty())
                    <section>
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-surface-500 dark:text-surface-400">
                            À pronostiquer
                        </h3>

                        <div class="pronostic-swiper swiper mx-auto h-96 w-full max-w-sm">
                            <div class="swiper-wrapper">
                                @foreach ($matchesAFaire as $match)
                                    <div class="swiper-slide">
                                        <x-card class="flex h-full flex-col p-6">
                                            <div class="text-center">
                                                <p class="text-lg font-semibold text-surface-900 dark:text-white">
                                                    {{ $match->equipe1() }}
                                                </p>
                                                <p class="my-1 text-xs font-medium uppercase tracking-wide text-surface-400">
                                                    vs
                                                </p>
                                                <p class="text-lg font-semibold text-surface-900 dark:text-white">
                                                    {{ $match->equipe2() }}
                                                </p>
                                                <p class="mt-2 text-sm text-surface-500 dark:text-surface-400">
                                                    {{ $match->date_heure->format('d/m/Y H:i') }}
                                                </p>
                                            </div>

                                            <form
                                                method="POST"
                                                action="{{ route('pronostics.store', $match) }}"
                                                x-data="pronosticCard()"
                                                @submit.prevent="submit({{ $match->id }})"
                                                class="mt-auto flex flex-col items-center gap-4"
                                            >
                                                @csrf

                                                <div class="flex items-end justify-center gap-3">
                                                    <div>
                                                        <x-input-label :for="'af_score_j1_'.$match->id" :value="$match->equipe1()" />
                                                        <input
                                                            id="af_score_j1_{{ $match->id }}"
                                                            name="prono_score_j1"
                                                            type="number"
                                                            min="0"
                                                            max="3"
                                                            x-model="scoreJ1"
                                                            required
                                                            class="mt-1 block w-20 rounded-md border-surface-300 text-center shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                                        />
                                                    </div>

                                                    <span class="pb-2 text-surface-400">—</span>

                                                    <div>
                                                        <x-input-label :for="'af_score_j2_'.$match->id" :value="$match->equipe2()" />
                                                        <input
                                                            id="af_score_j2_{{ $match->id }}"
                                                            name="prono_score_j2"
                                                            type="number"
                                                            min="0"
                                                            max="3"
                                                            x-model="scoreJ2"
                                                            required
                                                            class="mt-1 block w-20 rounded-md border-surface-300 text-center shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                                        />
                                                    </div>
                                                </div>

                                                <p x-cloak x-show="error" x-text="error" class="text-sm text-danger-600 dark:text-danger-400"></p>

                                                <x-primary-button x-bind:disabled="loading">
                                                    <span x-show="!loading">Valider</span>
                                                    <span x-cloak x-show="loading">Enregistrement…</span>
                                                </x-primary-button>
                                            </form>
                                        </x-card>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-center gap-6">
                            <button
                                type="button"
                                data-swiper-prev
                                class="rounded-full p-2 text-surface-500 hover:bg-surface-100 hover:text-surface-900 dark:text-surface-400 dark:hover:bg-surface-800 dark:hover:text-white"
                            >
                                <span class="sr-only">Match précédent</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 0 1 0 1.06L9.06 10l3.73 3.71a.75.75 0 1 1-1.06 1.06l-4.25-4.25a.75.75 0 0 1 0-1.06l4.25-4.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <span class="text-xs text-surface-400">
                                Glisse ou utilise les flèches pour naviguer entre les matchs
                            </span>

                            <button
                                type="button"
                                data-swiper-next
                                class="rounded-full p-2 text-surface-500 hover:bg-surface-100 hover:text-surface-900 dark:text-surface-400 dark:hover:bg-surface-800 dark:hover:text-white"
                            >
                                <span class="sr-only">Match suivant</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 0-1.06L10.94 10 7.21 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0Z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </section>
                @endif

                @if ($matchesTraites->isNotEmpty())
                    <section>
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-surface-500 dark:text-surface-400">
                            Mes pronostics
                        </h3>

                        <x-responsive-table>
                            <x-slot:table>
                                <thead class="bg-surface-50 dark:bg-surface-800">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Match</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Pronostic</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Statut</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                                    @foreach ($matchesTraites as $match)
                                        @php($prono = $match->pronostics->first())
                                        @php($verrouille = $match->isVerrouille())
                                        @php($modifiable = $prono && ! $verrouille && ! $match->resultat_saisi)

                                        <tr
                                            @if ($modifiable)
                                                x-data="pronosticRow({{ $prono->prono_score_j1 }}, {{ $prono->prono_score_j2 }})"
                                            @endif
                                        >
                                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-surface-900 dark:text-white">
                                                {{ $match->equipe1() }} vs {{ $match->equipe2() }}
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-500 dark:text-surface-400">
                                                {{ $match->date_heure->format('d/m/Y H:i') }}
                                            </td>

                                            @if ($modifiable)
                                                <td class="px-6 py-4 text-sm text-surface-700 dark:text-surface-300">
                                                    <span x-cloak x-show="!editing" x-text="savedScoreJ1 + ' - ' + savedScoreJ2"></span>
                                                    <form
                                                        x-cloak
                                                        x-show="editing"
                                                        @submit.prevent="submit({{ $match->id }})"
                                                        class="flex items-center gap-2"
                                                    >
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            max="3"
                                                            x-model="scoreJ1"
                                                            required
                                                            class="block w-16 rounded-md border-surface-300 text-center text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                                        />
                                                        <span class="text-surface-400">—</span>
                                                        <input
                                                            type="number"
                                                            min="0"
                                                            max="3"
                                                            x-model="scoreJ2"
                                                            required
                                                            class="block w-16 rounded-md border-surface-300 text-center text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                                        />

                                                        <button
                                                            type="submit"
                                                            x-bind:disabled="loading"
                                                            class="text-xs font-semibold uppercase tracking-widest text-primary-600 hover:text-primary-500 dark:text-primary-400"
                                                        >
                                                            <span x-show="!loading">Enregistrer</span>
                                                            <span x-cloak x-show="loading">…</span>
                                                        </button>
                                                        <button
                                                            type="button"
                                                            @click="cancelEdit()"
                                                            class="text-xs font-semibold uppercase tracking-widest text-surface-400 hover:text-surface-600 dark:hover:text-surface-300"
                                                        >
                                                            Annuler
                                                        </button>
                                                    </form>
                                                    <p x-cloak x-show="error" x-text="error" class="mt-1 text-xs text-danger-600 dark:text-danger-400"></p>
                                                </td>
                                            @else
                                                <td class="px-6 py-4 text-sm text-surface-700 dark:text-surface-300">
                                                    @if ($prono)
                                                        {{ $prono->prono_score_j1 }} - {{ $prono->prono_score_j2 }}
                                                    @else
                                                        <span class="text-surface-400">—</span>
                                                    @endif
                                                </td>
                                            @endif

                                            <td class="whitespace-nowrap px-6 py-4">
                                                @if ($match->resultat_saisi)
                                                    <span class="rounded-full bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-900/40 dark:text-success-400">
                                                        Résultat : {{ $match->score_j1 }} - {{ $match->score_j2 }}
                                                    </span>
                                                    @if ($prono)
                                                        <span class="ml-1 rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">
                                                            {{ $prono->points_obtenus ?? 0 }} pt(s)
                                                        </span>
                                                    @endif
                                                @elseif ($verrouille)
                                                    <span class="rounded-full bg-warning-100 px-2 py-0.5 text-xs font-medium text-warning-700 dark:bg-warning-900/40 dark:text-warning-400">
                                                        Verrouillé
                                                    </span>
                                                @else
                                                    <span class="rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">
                                                        En attente du résultat
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                                @if ($modifiable)
                                                    <button
                                                        type="button"
                                                        x-cloak
                                                        x-show="!editing"
                                                        @click="startEdit()"
                                                        class="text-xs font-semibold uppercase tracking-widest text-surface-500 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white"
                                                    >
                                                        Modifier
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </x-slot:table>

                            <x-slot:cards>
                                @foreach ($matchesTraites as $match)
                                @php($prono = $match->pronostics->first())
                                @php($verrouille = $match->isVerrouille())
                                @php($modifiable = $prono && ! $verrouille && ! $match->resultat_saisi)

                                <x-card
                                    class="p-4"
                                    x-data="{{ $modifiable ? 'pronosticRow('.$prono->prono_score_j1.', '.$prono->prono_score_j2.')' : '{}' }}"
                                >
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-medium text-surface-900 dark:text-white">
                                                {{ $match->equipe1() }} vs {{ $match->equipe2() }}
                                            </p>
                                            <p class="text-xs text-surface-500 dark:text-surface-400">
                                                {{ $match->date_heure->format('d/m/Y H:i') }}
                                            </p>
                                        </div>

                                        @if ($match->resultat_saisi)
                                            <span class="shrink-0 rounded-full bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-900/40 dark:text-success-400">
                                                {{ $match->score_j1 }} - {{ $match->score_j2 }}
                                            </span>
                                        @elseif ($verrouille)
                                            <span class="shrink-0 rounded-full bg-warning-100 px-2 py-0.5 text-xs font-medium text-warning-700 dark:bg-warning-900/40 dark:text-warning-400">
                                                Verrouillé
                                            </span>
                                        @else
                                            <span class="shrink-0 rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">
                                                En attente
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-3 flex items-center justify-between gap-2 border-t border-surface-100 pt-3 dark:border-surface-800">
                                        @if ($modifiable)
                                            <span x-cloak x-show="!editing" class="text-sm text-surface-700 dark:text-surface-300">
                                                Pronostic : <span x-text="savedScoreJ1 + ' - ' + savedScoreJ2"></span>
                                            </span>
                                            <button
                                                type="button"
                                                x-cloak
                                                x-show="!editing"
                                                @click="startEdit()"
                                                class="text-xs font-semibold uppercase tracking-widest text-surface-500 hover:text-surface-900 dark:text-surface-400 dark:hover:text-white"
                                            >
                                                Modifier
                                            </button>

                                            <form
                                                x-cloak
                                                x-show="editing"
                                                @submit.prevent="submit({{ $match->id }})"
                                                class="flex w-full flex-wrap items-center gap-2"
                                            >
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="3"
                                                    x-model="scoreJ1"
                                                    required
                                                    class="block w-16 rounded-md border-surface-300 text-center text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                                />
                                                <span class="text-surface-400">—</span>
                                                <input
                                                    type="number"
                                                    min="0"
                                                    max="3"
                                                    x-model="scoreJ2"
                                                    required
                                                    class="block w-16 rounded-md border-surface-300 text-center text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                                />

                                                <button
                                                    type="submit"
                                                    x-bind:disabled="loading"
                                                    class="text-xs font-semibold uppercase tracking-widest text-primary-600 hover:text-primary-500 dark:text-primary-400"
                                                >
                                                    <span x-show="!loading">Enregistrer</span>
                                                    <span x-cloak x-show="loading">…</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="cancelEdit()"
                                                    class="text-xs font-semibold uppercase tracking-widest text-surface-400 hover:text-surface-600 dark:hover:text-surface-300"
                                                >
                                                    Annuler
                                                </button>

                                                <p x-cloak x-show="error" x-text="error" class="w-full text-xs text-danger-600 dark:text-danger-400"></p>
                                            </form>
                                        @elseif ($prono)
                                            <span class="text-sm text-surface-700 dark:text-surface-300">
                                                Pronostic : {{ $prono->prono_score_j1 }} - {{ $prono->prono_score_j2 }}
                                            </span>
                                            @if ($match->resultat_saisi)
                                                <span class="text-xs text-surface-500 dark:text-surface-400">
                                                    {{ $prono->points_obtenus ?? 0 }} pt(s)
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-sm text-surface-400">Pas de pronostic</span>
                                        @endif
                                    </div>
                                </x-card>
                            @endforeach
                            </x-slot:cards>
                        </x-responsive-table>
                    </section>
                @endif
            @endif
        </div>
    </div>

    @vite(['resources/js/pronostics.js'])
</x-app-layout>
