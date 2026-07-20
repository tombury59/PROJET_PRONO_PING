@php($match = $match ?? null)

<div>
    <x-input-label for="phase_id" value="Phase" />
    <select
        id="phase_id"
        name="phase_id"
        class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
        required
    >
        <option value="">— Choisir une phase —</option>
        @foreach ($phases as $phase)
            <option value="{{ $phase->id }}" @selected(old('phase_id', $match->phase_id ?? null) == $phase->id)>
                {{ $phase->nom }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('phase_id')" class="mt-2" />
</div>

@php($estDouble = old('joueur_1_partenaire', $match->joueur_1_partenaire ?? null) || old('joueur_2_partenaire', $match->joueur_2_partenaire ?? null))

<div class="mt-4" x-data="{ double: @js((bool) $estDouble) }">
    <label class="flex items-center gap-2">
        <input
            type="checkbox"
            x-model="double"
            x-on:change="if (! double) { $refs.partenaire1.value = ''; $refs.partenaire2.value = ''; }"
            class="rounded border-neutral-300 text-neutral-900 shadow-sm focus:ring-neutral-500 dark:border-neutral-700"
        />
        <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300">Match en double (2 contre 2)</span>
    </label>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <x-input-label for="joueur_1" value="Joueur 1" />
            <x-text-input
                id="joueur_1"
                name="joueur_1"
                type="text"
                class="mt-1 block w-full"
                :value="old('joueur_1', $match->joueur_1 ?? '')"
                required
                autofocus
            />
            <x-input-error :messages="$errors->get('joueur_1')" class="mt-2" />

            <div x-show="double" x-transition class="mt-2">
                <x-input-label for="joueur_1_partenaire" value="Partenaire joueur 1" />
                <x-text-input
                    id="joueur_1_partenaire"
                    name="joueur_1_partenaire"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('joueur_1_partenaire', $match->joueur_1_partenaire ?? '')"
                    x-ref="partenaire1"
                />
                <x-input-error :messages="$errors->get('joueur_1_partenaire')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="joueur_2" value="Joueur 2" />
            <x-text-input
                id="joueur_2"
                name="joueur_2"
                type="text"
                class="mt-1 block w-full"
                :value="old('joueur_2', $match->joueur_2 ?? '')"
                required
            />
            <x-input-error :messages="$errors->get('joueur_2')" class="mt-2" />

            <div x-show="double" x-transition class="mt-2">
                <x-input-label for="joueur_2_partenaire" value="Partenaire joueur 2" />
                <x-text-input
                    id="joueur_2_partenaire"
                    name="joueur_2_partenaire"
                    type="text"
                    class="mt-1 block w-full"
                    :value="old('joueur_2_partenaire', $match->joueur_2_partenaire ?? '')"
                    x-ref="partenaire2"
                />
                <x-input-error :messages="$errors->get('joueur_2_partenaire')" class="mt-2" />
            </div>
        </div>
    </div>
</div>

<div class="mt-4" x-data>
    <x-input-label for="date_heure" value="Date et heure du match" />
    <x-text-input
        id="date_heure"
        name="date_heure"
        type="datetime-local"
        class="mt-1 block w-full"
        :value="old('date_heure', optional($match->date_heure ?? null)->format('Y-m-d\TH:i'))"
        required
        x-on:change="
            if (! $event.target.value) return;
            const d = new Date($event.target.value);
            d.setHours(d.getHours() - 1);
            const pad = (n) => String(n).padStart(2, '0');
            document.getElementById('date_fin_pronostics').value =
                `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
        "
    />
    <x-input-error :messages="$errors->get('date_heure')" class="mt-2" />

    <div class="mt-4">
        <x-input-label for="date_fin_pronostics" value="Fin des pronostics" />
        <x-text-input
            id="date_fin_pronostics"
            name="date_fin_pronostics"
            type="datetime-local"
            class="mt-1 block w-full"
            :value="old('date_fin_pronostics', optional($match->date_fin_pronostics ?? null)->format('Y-m-d\TH:i'))"
            required
        />
        <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
            Pré-rempli à 1h avant le match, modifiable si besoin. Les pronostics sont ouverts dès la création du match.
        </p>
        <x-input-error :messages="$errors->get('date_fin_pronostics')" class="mt-2" />
    </div>
</div>
