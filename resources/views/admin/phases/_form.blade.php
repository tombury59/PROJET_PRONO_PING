@php($phase = $phase ?? null)

<div>
    <x-input-label for="nom" value="Nom" />
    <x-text-input
        id="nom"
        name="nom"
        type="text"
        class="mt-1 block w-full"
        :value="old('nom', $phase->nom ?? '')"
        required
        autofocus
    />
    <x-input-error :messages="$errors->get('nom')" class="mt-2" />
</div>

<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
    <div>
        <x-input-label for="date_debut" value="Date de début" />
        <x-text-input
            id="date_debut"
            name="date_debut"
            type="date"
            class="mt-1 block w-full"
            :value="old('date_debut', optional($phase->date_debut ?? null)->format('Y-m-d'))"
            required
        />
        <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="date_fin" value="Date de fin" />
        <x-text-input
            id="date_fin"
            name="date_fin"
            type="date"
            class="mt-1 block w-full"
            :value="old('date_fin', optional($phase->date_fin ?? null)->format('Y-m-d'))"
            required
        />
        <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
    </div>
</div>

<div class="mt-4 flex items-center gap-2">
    <input
        id="reset_classement"
        name="reset_classement"
        type="checkbox"
        value="1"
        class="rounded border-neutral-300 text-neutral-900 shadow-sm focus:ring-neutral-500 dark:border-neutral-700"
        @checked(old('reset_classement', $phase->reset_classement ?? true))
    />
    <x-input-label for="reset_classement" value="Réinitialiser le classement au début de cette phase" />
</div>
