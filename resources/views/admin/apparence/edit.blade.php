<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-[color:var(--zone-header-fg)]">
            Apparence
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">
            @if (session('status') === 'apparence-updated')
                <div class="rounded-md bg-success-50 px-4 py-3 text-sm text-success-700 dark:bg-success-500/10 dark:text-success-400">
                    Apparence mise à jour.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.apparence.update') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                {{-- Couleurs (une par zone) --}}
                @php
                    $zones = [
                        ['btn', 'primary_color', old('primary_color', $couleurBouton), 'Boutons & accents', 'Boutons, onglet actif du menu, liens et survols.'],
                        ['header', 'color_header', old('color_header', $couleurHeader), 'Header (barre du haut)', 'Fond de la barre supérieure. Préfère une couleur claire.'],
                        ['navbar', 'color_navbar', old('color_navbar', $couleurNavbar), 'Navbar (menu latéral)', 'Fond du menu de gauche. Le texte s\'adapte automatiquement (clair/foncé).'],
                        ['page', 'color_page', old('color_page', $couleurFond), 'Fond des pages', 'Arrière-plan derrière les cartes.'],
                    ];
                @endphp

                <div x-data="{
                    fields: {
                        btn: '{{ $zones[0][2] }}',
                        header: '{{ $zones[1][2] }}',
                        navbar: '{{ $zones[2][2] }}',
                        page: '{{ $zones[3][2] }}',
                    },
                    norm(k) {
                        let v = this.fields[k].trim();
                        if (v && v[0] !== '#') v = '#' + v;
                        if (/^#[0-9a-fA-F]{6}$/.test(v)) this.fields[k] = v.toLowerCase();
                    },
                    contrast(hex) {
                        const c = (hex || '').replace('#', '');
                        if (c.length !== 6) return '#171717';
                        const r = parseInt(c.substr(0, 2), 16),
                              g = parseInt(c.substr(2, 2), 16),
                              b = parseInt(c.substr(4, 2), 16);
                        return (0.299 * r + 0.587 * g + 0.114 * b) / 255 > 0.6 ? '#171717' : '#ffffff';
                    },
                    lighten(hex, w) {
                        const c = hex.replace('#', '');
                        const m = (v) => Math.round(v + (255 - v) * w).toString(16).padStart(2, '0');
                        return '#' + m(parseInt(c.substr(0, 2), 16)) + m(parseInt(c.substr(2, 2), 16)) + m(parseInt(c.substr(4, 2), 16));
                    },
                    applyTheme(base) {
                        this.fields.btn = base;
                        this.fields.header = this.lighten(base, 0.80);
                        this.fields.navbar = this.lighten(base, 0.80);
                        this.fields.page = this.lighten(base, 0.90);
                    },
                }" class="grid gap-6 lg:grid-cols-2">
                <x-card class="p-6">
                    <h3 class="text-base font-semibold text-surface-900 dark:text-white">Couleurs</h3>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Chaque zone se règle indépendamment. Les nuances des boutons (survols, onglet actif) sont générées à partir de la couleur « Boutons&nbsp;».
                    </p>

                    <input type="hidden" name="primary_color" x-bind:value="fields.btn">
                    <input type="hidden" name="color_header" x-bind:value="fields.header">
                    <input type="hidden" name="color_navbar" x-bind:value="fields.navbar">
                    <input type="hidden" name="color_page" x-bind:value="fields.page">

                    <div class="mt-4 divide-y divide-surface-100 dark:divide-surface-800">
                        @foreach ($zones as [$key, $name, $value, $label, $help])
                            <div class="flex flex-wrap items-center gap-4 py-3">
                                <input
                                    type="color"
                                    x-model="fields.{{ $key }}"
                                    class="h-10 w-12 shrink-0 cursor-pointer rounded-md border border-surface-200 bg-white p-1 dark:border-surface-700 dark:bg-surface-800"
                                    aria-label="Couleur {{ $label }}"
                                >
                                <input
                                    type="text"
                                    x-model="fields.{{ $key }}"
                                    @change="norm('{{ $key }}')"
                                    @blur="norm('{{ $key }}')"
                                    maxlength="7"
                                    class="w-28 shrink-0 rounded-md border-surface-300 font-mono text-sm uppercase shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
                                >
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-medium text-surface-800 dark:text-surface-100">{{ $label }}</p>
                                    <p class="text-xs text-surface-500 dark:text-surface-400">{{ $help }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('primary_color') <p class="mt-2 text-sm text-danger-600">{{ $message }}</p> @enderror
                    @error('color_header') <p class="mt-2 text-sm text-danger-600">{{ $message }}</p> @enderror
                    @error('color_navbar') <p class="mt-2 text-sm text-danger-600">{{ $message }}</p> @enderror
                    @error('color_page') <p class="mt-2 text-sm text-danger-600">{{ $message }}</p> @enderror
                </x-card>

                {{-- Aperçu live --}}
                <x-card class="p-6">
                    <h3 class="text-base font-semibold text-surface-900 dark:text-white">Aperçu</h3>
                    <p class="mt-1 text-sm text-surface-500 dark:text-surface-400">
                        Rendu mis à jour en direct selon tes couleurs.
                    </p>

                    <div class="mt-4 overflow-hidden rounded-lg border border-surface-200 shadow-inner dark:border-surface-700" x-bind:style="`background-color: ${fields.page}`">
                        {{-- Header --}}
                        <div class="flex items-center justify-between px-3 py-2" x-bind:style="`background-color: ${fields.header}; color: ${contrast(fields.header)}`">
                            <span class="text-xs font-semibold">Tableau de bord</span>
                            <svg class="size-3.5 opacity-80" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1h6z" />
                            </svg>
                        </div>

                        <div class="flex" style="min-height: 190px">
                            {{-- Navbar --}}
                            <div class="w-28 shrink-0 space-y-1.5 p-2" x-bind:style="`background-color: ${fields.navbar}; color: ${contrast(fields.navbar)}`">
                                <div class="rounded px-2 py-1 text-[10px] font-medium" x-bind:style="`background-color: ${fields.btn}; color: ${contrast(fields.btn)}`">Accueil</div>
                                <div class="rounded px-2 py-1 text-[10px] opacity-80">Pronostics</div>
                                <div class="rounded px-2 py-1 text-[10px] opacity-80">Classement</div>
                                <div class="rounded px-2 py-1 text-[10px] opacity-80">Calendrier</div>
                            </div>

                            {{-- Contenu --}}
                            <div class="flex-1 p-3">
                                <div class="rounded-md border border-surface-200 bg-white p-3 shadow-sm">
                                    <div class="h-2 w-2/3 rounded bg-surface-200"></div>
                                    <div class="mt-2 h-2 w-1/2 rounded bg-surface-100"></div>
                                    <div class="mt-3 flex items-center gap-2">
                                        <button type="button" class="rounded px-3 py-1 text-[11px] font-medium" x-bind:style="`background-color: ${fields.btn}; color: ${contrast(fields.btn)}`">Bouton</button>
                                        <span class="text-[11px] font-medium" x-bind:style="`color: ${fields.btn}`">Lien</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Thèmes prédéfinis : un clic colore toutes les zones --}}
                    <div class="mt-4">
                        <span class="text-xs font-medium text-surface-500 dark:text-surface-400">Thèmes (appliqués à toutes les zones)</span>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($presets as $label => $hex)
                                <button
                                    type="button"
                                    @click="applyTheme('{{ $hex }}')"
                                    title="{{ $label }}"
                                    class="size-7 rounded-full border border-black/10 ring-offset-2 transition hover:scale-110 dark:ring-offset-surface-900"
                                    x-bind:class="fields.btn === '{{ $hex }}' && 'ring-2 ring-surface-900 dark:ring-white'"
                                    style="background-color: {{ $hex }}"
                                >
                                    <span class="sr-only">{{ $label }}</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </x-card>
                </div>

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
                                class="block text-sm text-surface-600 file:mr-4 file:rounded-md file:border-0 file:bg-primary-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-700 dark:text-surface-300 dark:file:bg-primary-500 dark:file:text-white"
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
