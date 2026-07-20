<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pronostic extends Model
{
    protected $fillable = [
        'user_id', 'match_id', 'prono_vainqueur',
        'prono_score_j1', 'prono_score_j2', 'points_obtenus',
    ];

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

        if ($this->prono_score_j1 === $match->score_j1 && $this->prono_score_j2 === $match->score_j2) {
            return 3;
        }

        if ($this->prono_vainqueur === $match->vainqueur()) {
            return 1;
        }

        return 0;
    }
}
