<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phase extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'date_debut', 'date_fin', 'reset_classement'];

    protected function casts(): array
    {
        return [
            'date_debut' => 'date',
            'date_fin' => 'date',
            'reset_classement' => 'boolean',
        ];
    }

    public function matches(): HasMany
    {
        return $this->hasMany(MatchGame::class);
    }

    public function questionsBonus(): HasMany
    {
        return $this->hasMany(QuestionBonus::class);
    }

    public static function courante(): ?self
    {
        return static::query()
            ->whereDate('date_debut', '<=', now())
            ->whereDate('date_fin', '>=', now())
            ->orderByDesc('date_debut')
            ->first() ?? static::query()->orderByDesc('date_debut')->first();
    }
}
