@if ($question->reponses->isEmpty())
    <p class="text-sm text-surface-500 dark:text-surface-400">Aucune réponse pour l'instant.</p>
@else
    <ul class="divide-y divide-surface-200 dark:divide-surface-800">
        @foreach ($question->reponses as $reponse)
            <li class="flex items-center justify-between gap-3 py-2 first:pt-0 last:pb-0">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-surface-900 dark:text-white">{{ $reponse->user->pseudo }}</p>
                    <p class="truncate text-xs text-surface-500 dark:text-surface-400">« {{ $reponse->reponse }} »</p>
                </div>

                <div class="flex shrink-0 items-center gap-3">
                    @if ($question->reponse_correcte)
                        @if ($reponse->points_obtenus > 0)
                            <span class="rounded-full bg-success-100 px-2 py-0.5 text-xs font-medium text-success-700 dark:bg-success-900/40 dark:text-success-400">
                                +5 pts
                            </span>
                        @else
                            <span class="rounded-full bg-surface-100 px-2 py-0.5 text-xs font-medium text-surface-600 dark:bg-surface-800 dark:text-surface-400">
                                0 pt
                            </span>
                        @endif

                        <form method="POST" action="{{ route('admin.questions-bonus.reponses.update', [$question, $reponse]) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="accordee" value="{{ $reponse->points_obtenus > 0 ? '0' : '1' }}">
                            <button type="submit" class="text-xs font-semibold uppercase tracking-widest text-primary-600 hover:text-primary-500 dark:text-primary-400">
                                {{ $reponse->points_obtenus > 0 ? 'Retirer' : 'Accorder' }}
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-surface-400">En attente de résolution</span>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
@endif
