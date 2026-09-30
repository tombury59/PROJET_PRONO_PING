<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-surface-900 dark:text-white">
            Apparence
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status') === 'apparence-updated')
                <div class="rounded-md bg-success-50 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:text-success-400">
                    Apparence mise à jour.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.apparence.update') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- Couleur principale --}}
                <x-card class="p-6">
                    <h3 class="text-base font-semibold text-surface-900 dark:text-white">Couleur principale</h3>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Utilisée pour les boutons, liens et éléments mis en avant.
                    </p>

                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
                        @foreach ($palettes as $key => $palette)
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="primary_color"
                                    value="{{ $key }}"
                                    class="peer sr-only"
                                    @checked(old('primary_color', $couleurActuelle) === $key)
                                >
                                <div class="flex items-center gap-3 rounded-lg border border-surface-200 p-3 transition peer-checked:border-surface-900 peer-checked:ring-2 peer-checked:ring-surface-900 dark:border-surface-700 dark:peer-checked:border-white dark:peer-checked:ring-white">
                                    <span class="size-6 shrink-0 rounded-full" style="background-color: {{ $palette['shades'][600] }}"></span>
                                    <span class="text-sm font-medium text-surface-700 dark:text-surface-200">{{ $palette['label'] }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('primary_color')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </x-card>

                {{-- Logo --}}
                <x-card class="p-6" x-data="{ preview: null }">
                    <h3 class="text-base font-semibold text-surface-900 dark:text-white">Logo</h3>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Affiché dans le menu et sur la page de connexion. PNG, JPG, WEBP ou SVG, 2&nbsp;Mo max.
                    </p>

                    <div class="mt-4 flex flex-wrap items-center gap-6">
                        <div class="flex size-20 shrink-0 items-center justify-center rounded-lg border border-surface-200 bg-surface-50 p-2 dark:border-surface-700 dark:bg-surface-800">
                            <template x-if="preview">
                                <img :src="preview" alt="Aperçu" class="max-h-full max-w-full object-contain">
                            </template>
                            <template x-if="! preview">
                                <x-application-logo class="h-12 w-12 fill-current text-surface-400" />
                            </template>
                        </div>

                        <div class="space-y-3">
                            <input
                                type="file"
                                name="logo"
                                accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                @change="preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : null"
                                class="block text-sm text-surface-600 file:mr-4 file:rounded-md file:border-0 file:bg-surface-900 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-surface-700 dark:text-surface-300 dark:file:bg-white dark:file:text-surface-900"
                            >

                            @if ($logoPath)
                                <label class="flex items-center gap-2 text-sm text-surface-600 dark:text-surface-300">
                                    <input type="checkbox" name="supprimer_logo" value="1" class="rounded border-surface-300 text-surface-900 focus:ring-surface-900">
                                    Supprimer le logo actuel (revenir au logo par défaut)
                                </label>
                            @endif
                        </div>
                    </div>
                    @error('logo')
                        <p class="mt-2 text-sm text-danger-600">{{ $message }}</p>
                    @enderror
                </x-card>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-md bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-700 dark:bg-primary-500 dark:text-white dark:hover:bg-primary-600">
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
