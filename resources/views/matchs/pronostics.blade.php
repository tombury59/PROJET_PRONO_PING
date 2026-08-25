<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-2">
            <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
                Pronostics — {{ $match->equipe1() }} vs {{ $match->equipe2() }}
            </h2>
            <a
                href="{{ route('pronostics.index') }}"
                class="shrink-0 text-sm font-medium text-surface-600 underline-offset-2 hover:underline dark:text-surface-300"
            >
                Retour
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl space-y-4 sm:px-6 lg:px-8">
            <x-card class="p-6">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-sm text-surface-500 dark:text-surface-400">
                        {{ $match->date_heure->format('d/m/Y H:i') }}
                    </p>
                    @if ($match->resultat_saisi)
                        <span class="rounded-full bg-success-100 px-3 py-1 text-sm font-semibold text-success-700 dark:bg-success-900/40 dark:text-success-400">
                            Résultat : {{ $match->score_j1 }} - {{ $match->score_j2 }}
                        </span>
                    @else
                        <span class="rounded-full bg-warning-100 px-3 py-1 text-sm font-medium text-warning-700 dark:bg-warning-900/40 dark:text-warning-400">
                            Match en cours — résultat à venir
                        </span>
                    @endif
                </div>
            </x-card>

            <x-responsive-table>
                <x-slot:table>
                    <thead class="bg-surface-50 dark:bg-surface-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Joueur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Pronostic</th>
                            @if ($match->resultat_saisi)
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Points</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                        @forelse ($pronostics as $prono)
                            <tr class="{{ $prono->user_id === auth()->id() ? 'bg-surface-50 dark:bg-surface-800/60' : '' }}">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-surface-900 dark:text-white">
                                    <div class="flex items-center gap-3">
                                        <x-avatar :user="$prono->user" class="size-8" />
                                        <span>
                                            {{ $prono->user->pseudo }}
                                            @if ($prono->user_id === auth()->id())
                                                <span class="ml-1 text-xs text-surface-400">(toi)</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-700 dark:text-surface-300">
                                    {{ $prono->prono_score_j1 }} - {{ $prono->prono_score_j2 }}
                                </td>
                                @if ($match->resultat_saisi)
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-surface-700 dark:text-surface-300">
                                        {{ $prono->points_obtenus ?? 0 }}
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-surface-500 dark:text-surface-400">
                                    Personne n'a pronostiqué ce match.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-slot:table>

                <x-slot:cards>
                    @forelse ($pronostics as $prono)
                        <x-card class="flex items-center justify-between p-4 {{ $prono->user_id === auth()->id() ? 'bg-surface-50 dark:bg-surface-800/60' : '' }}">
                            <div class="flex items-center gap-3">
                                <x-avatar :user="$prono->user" class="size-9" />
                                <div>
                                    <p class="text-sm font-medium text-surface-900 dark:text-white">
                                        {{ $prono->user->pseudo }}
                                        @if ($prono->user_id === auth()->id())
                                            <span class="ml-1 text-xs text-surface-400">(toi)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-surface-500 dark:text-surface-400">
                                        Pronostic : {{ $prono->prono_score_j1 }} - {{ $prono->prono_score_j2 }}
                                    </p>
                                </div>
                            </div>
                            @if ($match->resultat_saisi)
                                <span class="text-sm font-semibold text-surface-700 dark:text-surface-300">
                                    {{ $prono->points_obtenus ?? 0 }} pt(s)
                                </span>
                            @endif
                        </x-card>
                    @empty
                        <x-card class="p-6 text-center text-sm text-surface-500 dark:text-surface-400">
                            Personne n'a pronostiqué ce match.
                        </x-card>
                    @endforelse
                </x-slot:cards>
            </x-responsive-table>
        </div>
    </div>
</x-app-layout>
