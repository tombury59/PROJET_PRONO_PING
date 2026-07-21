@php($question = $question ?? null)

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
            <option value="{{ $phase->id }}" @selected(old('phase_id', $question->phase_id ?? null) == $phase->id)>
                {{ $phase->nom }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('phase_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="match_id" value="Match lié (optionnel)" />
    <select
        id="match_id"
        name="match_id"
        class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
    >
        <option value="">— Aucun (question générale de phase) —</option>
        @foreach ($matches as $match)
            <option value="{{ $match->id }}" @selected(old('match_id', $question->match_id ?? null) == $match->id)>
                {{ $match->equipe1() }} vs {{ $match->equipe2() }} — {{ $match->date_heure->format('d/m/Y H:i') }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('match_id')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="question" value="Question" />
    <textarea
        id="question"
        name="question"
        rows="2"
        class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white"
        required
    >{{ old('question', $question->question ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('question')" class="mt-2" />
</div>

<div class="mt-4">
    <x-input-label for="reponse_correcte" value="Bonne réponse" />
    <x-text-input
        id="reponse_correcte"
        name="reponse_correcte"
        type="text"
        class="mt-1 block w-full"
        :value="old('reponse_correcte', $question->reponse_correcte ?? '')"
    />
    <p class="mt-1 text-xs text-neutral-500 dark:text-neutral-400">
        Laisse vide tant que tu ne connais pas la réponse : la question reste ouverte aux joueurs.
        Dès que tu la renseignes, les points (5) sont calculés automatiquement pour tous ceux qui ont déjà répondu.
    </p>
    <x-input-error :messages="$errors->get('reponse_correcte')" class="mt-2" />
</div>
