<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchGame extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'phase_id', 'equipe_1', 'equipe_2', 'nb_matchs',
        'date_heure', 'date_fin_pronostics', 'score_j1', 'score_j2', 'resultat_saisi',
        'rappel_j2_envoye', 'rappel_24h_envoye',
    ];

    protected function casts(): array
    {
        return [
            'date_heure' => 'datetime',
            'date_fin_pronostics' => 'datetime',
            'nb_matchs' => 'integer',
            'resultat_saisi' => 'boolean',
            'rappel_j2_envoye' => 'boolean',
            'rappel_24h_envoye' => 'boolean',
        ];
    }

    public function phase(): BelongsTo
    {
        return $this->belongsTo(Phase::class);
    }

    public function pronostics(): HasMany
    {
        return $this->hasMany(Pronostic::class, 'match_id');
    }

    public function questionsBonus(): HasMany
    {
        return $this->hasMany(QuestionBonus::class, 'match_id');
    }

    public function isVerrouille(): bool
    {
        return now()->greaterThanOrEqualTo($this->date_fin_pronostics);
    }

    /**
     * Vainqueur de la rencontre : 1 (équipe 1), 2 (équipe 2), 0 (match nul),
     * ou null si le résultat n'est pas encore saisi.
     */
    public function vainqueur(): ?int
    {
        if (! $this->resultat_saisi) {
            return null;
        }

        if ($this->score_j1 === $this->score_j2) {
            return 0;
        }

        return $this->score_j1 > $this->score_j2 ? 1 : 2;
    }

    public function equipe1(): string
    {
        return $this->equipe_1;
    }

    public function equipe2(): string
    {
        return $this->equipe_2;
    }
}
