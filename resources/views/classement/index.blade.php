<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Classement
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <x-card class="mt-5 space-y-4 p-4">
            @if ($phases->isNotEmpty())
                <form method="GET" action="{{ route('classement.index') }}" class="max-w-xs">
                    <x-input-label for="vue" value="Vue" />
                    <select
                        id="vue"
                        name="vue"
                        onchange="this.form.submit()"
                        class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                    >
                        @foreach ($phases as $p)
                            <option value="{{ $p->id }}" @selected((string) $selection === (string) $p->id)>
                                {{ $p->nom }}
                            </option>
                        @endforeach
                        <option value="global" @selected($selection === 'global')>
                            Toutes les phases (global)
                        </option>
                    </select>
                </form>
            @endif

            @if ($phasesIncluses->count() > 1)
                <div class="rounded-md bg-warning-50 px-4 py-3 text-sm text-warning-700 dark:bg-warning-900/20 dark:text-warning-400">
                    Classement cumulé sur : {{ $phasesIncluses->pluck('nom')->join(' → ') }}
                    (reset non activé sur {{ $phase->nom }}).
                </div>
            @endif

            @if ($monRang)
                <div class="rounded-md bg-surface-100 px-4 py-3 text-sm text-surface-700 dark:bg-surface-800 dark:text-surface-300">
                    Tu es actuellement <span class="font-semibold">#{{ $monRang }}</span> sur {{ $classement->count() }}.
                </div>
            @endif

            <x-responsive-table>
                <x-slot:table>
                    <thead class="bg-surface-50 dark:bg-surface-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Rang</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Pseudo</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Points</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                        @forelse ($classement as $i => $entree)
                            <tr class="{{ $entree['user']->id === auth()->id() ? 'bg-surface-50 dark:bg-surface-800/60' : '' }}">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-surface-500 dark:text-surface-400">
                                    #{{ $i + 1 }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-surface-900 dark:text-white">
                                    {{ $entree['user']->pseudo }}
                                    @if ($entree['user']->id === auth()->id())
                                        <span class="ml-1 text-xs text-surface-400">(toi)</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-surface-700 dark:text-surface-300">
                                    {{ $entree['points'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sm text-surface-500 dark:text-surface-400">
                                    Aucun classement disponible pour l'instant.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-slot:table>

                <x-slot:cards>
                    @forelse ($classement as $i => $entree)
                        <x-card class="flex items-center justify-between p-4 {{ $entree['user']->id === auth()->id() ? 'bg-surface-50 dark:bg-surface-800/60' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-surface-500 dark:text-surface-400">
                                    #{{ $i + 1 }}
                                </span>
                                <span class="text-sm font-medium text-surface-900 dark:text-white">
                                    {{ $entree['user']->pseudo }}
                                    @if ($entree['user']->id === auth()->id())
                                        <span class="ml-1 text-xs text-surface-400">(toi)</span>
                                    @endif
                                </span>
                            </div>
                            <span class="text-sm font-semibold text-surface-700 dark:text-surface-300">
                                {{ $entree['points'] }}
                            </span>
                        </x-card>
                    @empty
                        <x-card class="p-6 text-center text-sm text-surface-500 dark:text-surface-400">
                            Aucun classement disponible pour l'instant.
                        </x-card>
                    @endforelse
                </x-slot:cards>
            </x-responsive-table>
            </x-card>
        </div>
    </div>
</x-app-layout>
