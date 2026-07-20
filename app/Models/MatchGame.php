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
        'phase_id', 'joueur_1', 'joueur_1_partenaire', 'joueur_2', 'joueur_2_partenaire',
        'date_heure', 'date_fin_pronostics', 'score_j1', 'score_j2', 'resultat_saisi',
    ];

    protected function casts(): array
    {
        return [
            'date_heure' => 'datetime',
            'date_fin_pronostics' => 'datetime',
            'resultat_saisi' => 'boolean',
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

    public function vainqueur(): ?int
    {
        if (! $this->resultat_saisi) {
            return null;
        }

        return $this->score_j1 > $this->score_j2 ? 1 : 2;
    }

    public function estDouble(): bool
    {
        return ! empty($this->joueur_1_partenaire) || ! empty($this->joueur_2_partenaire);
    }

    public function equipe1(): string
    {
        return $this->joueur_1_partenaire
            ? "{$this->joueur_1} / {$this->joueur_1_partenaire}"
            : $this->joueur_1;
    }

    public function equipe2(): string
    {
        return $this->joueur_2_partenaire
            ? "{$this->joueur_2} / {$this->joueur_2_partenaire}"
            : $this->joueur_2;
    }
}
