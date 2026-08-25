@php($match = $match ?? null)
@php($dateHeurePreremplie = $dateHeurePreremplie ?? null)
@php($phaseIdPreremplie = $phaseIdPreremplie ?? null)

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
            <option value="{{ $phase->id }}" @selected(old('phase_id', $match->phase_id ?? $phaseIdPreremplie) == $phase->id)>
                {{ $phase->nom }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('phase_id')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="equipe_1" value="Équipe 1 (domicile)" />
        <x-text-input
            id="equipe_1"
            name="equipe_1"
            type="text"
            class="mt-1 block w-full"
            :value="old('equipe_1', $match->equipe_1 ?? '')"
            placeholder="Ex. Lille 2"
            required
            autofocus
        />
        <x-input-error :messages="$errors->get('equipe_1')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="equipe_2" value="Équipe 2 (extérieur)" />
        <x-text-input
            id="equipe_2"
            name="equipe_2"
            type="text"
            class="mt-1 block w-full"
            :value="old('equipe_2', $match->equipe_2 ?? '')"
            placeholder="Ex. Roubaix 3"
            required
        />
        <x-input-error :messages="$errors->get('equipe_2')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <x-input-label for="nb_matchs" value="Format de la rencontre" />
    <select
        id="nb_matchs"
        name="nb_matchs"
        class="mt-1 block w-full rounded-md border-surface-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-surface-700 dark:bg-surface-800 dark:text-white"
        required
    >
        @foreach ([18 => 'En 18 matchs (nul à 9-9)', 14 => 'En 14 matchs — région (nul à 7-7)'] as $valeur => $libelle)
            <option value="{{ $valeur }}" @selected(old('nb_matchs', $match->nb_matchs ?? 18) == $valeur)>
                {{ $libelle }}
            </option>
        @endforeach
    </select>
    <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
        Nombre total de matchs de la rencontre. Le score de chaque équipe va de 0 à ce total.
    </p>
    <x-input-error :messages="$errors->get('nb_matchs')" class="mt-2" />
</div>

<div class="mt-4" x-data>
    <x-input-label for="date_heure" value="Date et heure du match" />
    <x-text-input
        id="date_heure"
        name="date_heure"
        type="datetime-local"
        class="mt-1 block w-full"
        :value="old('date_heure', optional($match->date_heure ?? $dateHeurePreremplie)->format('Y-m-d\TH:i'))"
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
            :value="old('date_fin_pronostics', optional($match->date_fin_pronostics ?? $dateHeurePreremplie?->copy()->subHour())->format('Y-m-d\TH:i'))"
            required
        />
        <p class="mt-1 text-xs text-surface-500 dark:text-surface-400">
            Pré-rempli à 1h avant le match, modifiable si besoin. Les pronostics sont ouverts dès la création du match.
        </p>
        <x-input-error :messages="$errors->get('date_fin_pronostics')" class="mt-2" />
    </div>
</div>
