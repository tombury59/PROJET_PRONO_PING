<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pronostic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'match_id', 'prono_vainqueur',
        'prono_score_j1', 'prono_score_j2', 'joker', 'points_obtenus',
    ];

    protected function casts(): array
    {
        return [
            'joker' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function calculerPoints(): int
    {
        $match = $this->match;

        if (! $match->resultat_saisi) {
            return 0;
        }

        $base = 0;

        if ($this->prono_score_j1 === $match->score_j1 && $this->prono_score_j2 === $match->score_j2) {
            $base = 3;
        } elseif ($this->prono_vainqueur === $match->vainqueur()) {
            $base = 1;
        }

        // Le joker double les points de ce pronostic.
        return $this->joker ? $base * 2 : $base;
    }
}
