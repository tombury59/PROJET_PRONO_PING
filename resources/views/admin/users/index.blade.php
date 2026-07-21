<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Utilisateurs
        </h2>
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

            <form method="GET" action="{{ route('admin.users.index') }}" class="max-w-xs">
                <x-input-label for="search" value="Rechercher un utilisateur" />
                <input
                    id="search"
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Pseudo..."
                    class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                />
            </form>

            <x-responsive-table>
                <x-slot:table>
                    <thead class="bg-surface-50 dark:bg-surface-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Pseudo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Rôle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Inscrit le</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Pronostics</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-surface-500 dark:text-surface-400">Réponses bonus</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-200 dark:divide-surface-800">
                        @forelse ($users as $user)
                            <tr>
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-surface-900 dark:text-white">
                                    {{ $user->pseudo }}
                                    @if ($user->id === auth()->id())
                                        <span class="ml-1 text-xs text-surface-400">(toi)</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    @if ($user->id === auth()->id())
                                        <span class="rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">
                                            {{ $user->isAdmin() ? 'Admin' : 'Joueur' }}
                                        </span>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.role.update', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <select
                                                name="role"
                                                onchange="this.form.submit()"
                                                class="rounded-md border-surface-300 py-1 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                            >
                                                <option value="joueur" @selected(! $user->isAdmin())>Joueur</option>
                                                <option value="admin" @selected($user->isAdmin())>Admin</option>
                                            </select>
                                        </form>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">
                                    {{ $user->created_at->format('d/m/Y') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">
                                    {{ $user->pronostics_count }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-surface-600 dark:text-surface-300">
                                    {{ $user->reponses_bonus_count }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    @unless ($user->id === auth()->id())
                                        <form
                                            method="POST"
                                            action="{{ route('admin.users.destroy', $user) }}"
                                            onsubmit="return confirm('Supprimer {{ $user->pseudo }} ? Ses pronostics et réponses bonus seront aussi supprimés.');"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-danger-600 underline-offset-2 hover:underline dark:text-danger-400">
                                                Supprimer
                                            </button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-surface-500 dark:text-surface-400">
                                    {{ $search !== '' ? 'Aucun utilisateur ne correspond à « '.$search.' ».' : 'Aucun utilisateur inscrit.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-slot:table>

                <x-slot:cards>
                    @forelse ($users as $user)
                        <x-card class="p-4">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-medium text-surface-900 dark:text-white">
                                    {{ $user->pseudo }}
                                    @if ($user->id === auth()->id())
                                        <span class="ml-1 text-xs text-surface-400">(toi)</span>
                                    @endif
                                </p>

                                @if ($user->id === auth()->id())
                                    <span class="shrink-0 rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">
                                        {{ $user->isAdmin() ? 'Admin' : 'Joueur' }}
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.role.update', $user) }}" class="shrink-0">
                                        @csrf
                                        @method('PATCH')
                                        <select
                                            name="role"
                                            onchange="this.form.submit()"
                                            class="rounded-md border-surface-300 py-1 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                        >
                                            <option value="joueur" @selected(! $user->isAdmin())>Joueur</option>
                                            <option value="admin" @selected($user->isAdmin())>Admin</option>
                                        </select>
                                    </form>
                                @endif
                            </div>

                            <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
                                Inscrit le {{ $user->created_at->format('d/m/Y') }}
                            </p>

                            <div class="mt-3 flex items-center justify-between gap-2 border-t border-surface-100 pt-3 dark:border-surface-800">
                                <span class="text-xs text-surface-500 dark:text-surface-400">
                                    {{ $user->pronostics_count }} pronostics · {{ $user->reponses_bonus_count }} réponses bonus
                                </span>

                                @unless ($user->id === auth()->id())
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.destroy', $user) }}"
                                        onsubmit="return confirm('Supprimer {{ $user->pseudo }} ? Ses pronostics et réponses bonus seront aussi supprimés.');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold uppercase tracking-widest text-danger-600 hover:text-danger-500 dark:text-danger-400">
                                            Supprimer
                                        </button>
                                    </form>
                                @endunless
                            </div>
                        </x-card>
                    @empty
                        <x-card class="p-6 text-center text-sm text-surface-500 dark:text-surface-400">
                            {{ $search !== '' ? 'Aucun utilisateur ne correspond à « '.$search.' ».' : 'Aucun utilisateur inscrit.' }}
                        </x-card>
                    @endforelse
                </x-slot:cards>
            </x-responsive-table>

            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
