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
    </div>
</div>

<div class="mt-4">
    <x-input-label for="date_heure" value="Date et heure" />
    <x-text-input
        id="date_heure"
        name="date_heure"
        type="datetime-local"
        class="mt-1 block w-full"
        :value="old('date_heure', optional($match->date_heure ?? null)->format('Y-m-d\TH:i'))"
        required
    />
    <x-input-error :messages="$errors->get('date_heure')" class="mt-2" />
</div>
