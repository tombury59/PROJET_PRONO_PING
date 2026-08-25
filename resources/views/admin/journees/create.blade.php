<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Nouvelle journée
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg dark:bg-surface-900">
                @if ($phases->isEmpty())
                    <p class="text-sm text-surface-600 dark:text-surface-300">
                        Aucune phase n'existe encore.
                        <a href="{{ route('admin.phases.create') }}" class="font-medium underline">Crée d'abord une phase</a>.
                    </p>
                @else
                    <form
                        method="POST"
                        action="{{ route('admin.journees.store') }}"
                        x-data="journeeForm(@js(old('rencontres', [])), @js(old('bonus', [])), @js($defautDateHeure))"
                    >
                        @csrf

                        <div>
                            <x-input-label for="phase_id" value="Phase" />
                            <select
                                id="phase_id"
                                name="phase_id"
                                class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                required
                            >
                                <option value="">— Choisir une phase —</option>
                                @foreach ($phases as $phase)
                                    <option value="{{ $phase->id }}" @selected(old('phase_id', $phaseIdPreremplie) == $phase->id)>
                                        {{ $phase->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('phase_id')" class="mt-2" />
                        </div>

                        <div class="mt-4 rounded-md bg-surface-50 p-4 dark:bg-surface-800/50">
                            <x-input-label for="defaut_date_heure" value="Heure par défaut des rencontres" />
                            <div class="mt-1 flex flex-wrap items-center gap-3">
                                <input
                                    id="defaut_date_heure"
                                    type="datetime-local"
                                    x-model="defautDateHeure"
                                    class="block rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                />
                                <button
                                    type="button"
                                    @click="appliquerDefautAuxVides()"
                                    class="rounded-md border border-surface-300 px-3 py-2 text-sm font-medium text-surface-600 hover:bg-surface-100 dark:border-surface-700 dark:text-surface-300 dark:hover:bg-white/5"
                                >
                                    Appliquer aux lignes vides
                                </button>
                            </div>
                            <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
                                Chaque nouvelle rencontre reprend cette heure (modifiable ligne par ligne).
                                Créneaux habituels : samedi 17h-19h, dimanche 8h30-9h, dimanche 14h-14h30.
                            </p>
                        </div>

                        {{-- Rencontres --}}
                        <div class="mt-6">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                    Rencontres (<span x-text="rencontres.length"></span>)
                                </h3>
                                <button
                                    type="button"
                                    @click="ajouterRencontre()"
                                    class="rounded-md bg-surface-900 px-3 py-1.5 text-sm font-medium text-white hover:bg-surface-700 dark:bg-white dark:text-surface-900 dark:hover:bg-surface-200"
                                >
                                    + Ajouter une rencontre
                                </button>
                            </div>

                            <x-input-error :messages="$errors->get('rencontres')" class="mt-2" />

                            <div class="mt-3 space-y-3">
                                <template x-for="(rencontre, index) in rencontres" :key="index">
                                    <div class="rounded-md border border-surface-200 p-3 dark:border-surface-700">
                                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                            <div>
                                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400">Équipe 1 (domicile)</label>
                                                <input
                                                    type="text"
                                                    x-model="rencontre.equipe_1"
                                                    :name="`rencontres[${index}][equipe_1]`"
                                                    placeholder="Ex. Lille 2"
                                                    required
                                                    class="mt-1 block w-full rounded-md border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400">Équipe 2 (extérieur)</label>
                                                <input
                                                    type="text"
                                                    x-model="rencontre.equipe_2"
                                                    :name="`rencontres[${index}][equipe_2]`"
                                                    placeholder="Ex. Roubaix 3"
                                                    required
                                                    class="mt-1 block w-full rounded-md border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                                />
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400">Format</label>
                                                <select
                                                    x-model="rencontre.nb_matchs"
                                                    :name="`rencontres[${index}][nb_matchs]`"
                                                    class="mt-1 block w-full rounded-md border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                                >
                                                    <option value="18">En 18 (nul à 9-9)</option>
                                                    <option value="14">En 14 — région (nul à 7-7)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-surface-600 dark:text-surface-400">Date et heure</label>
                                                <input
                                                    type="datetime-local"
                                                    x-model="rencontre.date_heure"
                                                    :name="`rencontres[${index}][date_heure]`"
                                                    required
                                                    class="mt-1 block w-full rounded-md border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                                />
                                            </div>
                                        </div>

                                        <div class="mt-2 text-right">
                                            <button
                                                type="button"
                                                @click="supprimerRencontre(index)"
                                                class="text-xs font-semibold uppercase tracking-widest text-danger-600 hover:text-danger-500 dark:text-danger-400"
                                            >
                                                Retirer
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Questions bonus --}}
                        <div class="mt-8">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-surface-500 dark:text-surface-400">
                                    Questions bonus (<span x-text="bonus.length"></span>/3)
                                </h3>
                                <button
                                    type="button"
                                    @click="ajouterBonus()"
                                    x-show="bonus.length < 3"
                                    class="rounded-md border border-surface-300 px-3 py-1.5 text-sm font-medium text-surface-600 hover:bg-surface-100 dark:border-surface-700 dark:text-surface-300 dark:hover:bg-white/5"
                                >
                                    + Ajouter une question bonus
                                </button>
                            </div>

                            <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
                                Optionnel, jusqu'à 3. Elles apparaîtront pour les joueurs dans l'onglet Bonus. La bonne réponse se renseigne plus tard.
                            </p>

                            <div class="mt-3 space-y-3">
                                <template x-for="(q, index) in bonus" :key="index">
                                    <div class="rounded-md border border-surface-200 p-3 dark:border-surface-700">
                                        <div>
                                            <label class="block text-xs font-medium text-surface-600 dark:text-surface-400">Question</label>
                                            <input
                                                type="text"
                                                x-model="q.question"
                                                :name="`bonus[${index}][question]`"
                                                placeholder="Ex. Combien d'équipes gagnantes ce week-end ?"
                                                class="mt-1 block w-full rounded-md border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                            />
                                        </div>
                                        <div class="mt-2">
                                            <label class="block text-xs font-medium text-surface-600 dark:text-surface-400">Description (optionnel)</label>
                                            <textarea
                                                x-model="q.description"
                                                :name="`bonus[${index}][description]`"
                                                rows="2"
                                                class="mt-1 block w-full rounded-md border-surface-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                            ></textarea>
                                        </div>
                                        <div class="mt-2 text-right">
                                            <button
                                                type="button"
                                                @click="supprimerBonus(index)"
                                                class="text-xs font-semibold uppercase tracking-widest text-danger-600 hover:text-danger-500 dark:text-danger-400"
                                            >
                                                Retirer
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center gap-3">
                            <x-primary-button>Créer la journée</x-primary-button>
                            <a href="{{ route('admin.matches.index') }}" class="text-sm text-surface-600 hover:underline dark:text-surface-300">
                                Annuler
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @vite(['resources/js/journee.js'])
</x-app-layout>
