<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
                Questions bonus
            </h2>

            <a
                href="{{ route('admin.questions-bonus.create') }}"
                class="rounded-sm bg-surface-900 px-4 py-2 text-sm font-medium text-white hover:bg-surface-700 dark:bg-white dark:text-surface-900 dark:hover:bg-surface-200"
            >
                Nouvelle question
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-4 sm:px-6 lg:px-8">
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
                <form method="GET" action="{{ route('admin.questions-bonus.index') }}" class="max-w-xs">
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
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Question</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Match lié</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Réponses</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                        @forelse ($questions as $question)
                            <tr>
                                <td class="max-w-xs px-6 py-4 text-sm font-medium text-surface-900 dark:text-white">
                                    {{ $question->question }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">
                                    {{ $question->match ? $question->match->equipe1().' vs '.$question->match->equipe2() : '—' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    @if ($question->reponse_correcte)
                                        <span class="rounded-full bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-900/40 dark:text-success-400">
                                            Résolue : {{ $question->reponse_correcte }}
                                        </span>
                                    @else
                                        <span class="rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">
                                            En attente
                                        </span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">
                                    {{ $question->reponses_count }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    <a href="{{ route('admin.questions-bonus.edit', $question) }}" class="font-medium text-surface-700 underline-offset-2 hover:underline dark:text-surface-300">
                                        Modifier
                                    </a>

                                    <form method="POST" action="{{ route('admin.questions-bonus.destroy', $question) }}" class="ml-3 inline" onsubmit="return confirm('Supprimer cette question ?');">
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
                                    Aucune question bonus pour cette phase.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-slot:table>

                <x-slot:cards>
                    @forelse ($questions as $question)
                        <x-card class="p-4">
                            <p class="text-sm font-medium text-surface-900 dark:text-white">
                                {{ $question->question }}
                            </p>
                            <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
                                {{ $question->match ? $question->match->equipe1().' vs '.$question->match->equipe2() : '—' }}
                            </p>

                            <div class="mt-3 flex items-center justify-between gap-2">
                                @if ($question->reponse_correcte)
                                    <span class="rounded-full bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-900/40 dark:text-success-400">
                                        Résolue : {{ $question->reponse_correcte }}
                                    </span>
                                @else
                                    <span class="rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">
                                        En attente
                                    </span>
                                @endif

                                <span class="text-xs text-surface-500 dark:text-surface-400">
                                    {{ $question->reponses_count }} réponses
                                </span>
                            </div>

                            <div class="mt-3 flex items-center gap-3 border-t border-surface-100 pt-3 text-xs font-semibold uppercase tracking-widest dark:border-surface-800">
                                <a href="{{ route('admin.questions-bonus.edit', $question) }}" class="text-surface-700 hover:text-surface-900 dark:text-surface-300 dark:hover:text-white">
                                    Modifier
                                </a>

                                <form method="POST" action="{{ route('admin.questions-bonus.destroy', $question) }}" onsubmit="return confirm('Supprimer cette question ?');">
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
                            Aucune question bonus pour cette phase.
                        </x-card>
                    @endforelse
                </x-slot:cards>
            </x-responsive-table>
        </div>
    </div>
</x-app-layout>
