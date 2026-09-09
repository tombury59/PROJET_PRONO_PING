<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use NotificationChannels\WebPush\HasPushSubscriptions;

#[Fillable(['pseudo', 'avatar_path', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasPushSubscriptions, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar_path
            ? Storage::disk('public')->url($this->avatar_path)
            : null;
    }

    /**
     * Initiales du pseudo, pour l'avatar par défaut (ex. "TB").
     */
    public function initiales(): string
    {
        $mots = preg_split('/[\s\-_]+/', trim((string) $this->pseudo)) ?: [];
        $mots = array_values(array_filter($mots));

        if ($mots === []) {
            return '?';
        }

        if (count($mots) === 1) {
            return mb_strtoupper(mb_substr($mots[0], 0, 2));
        }

        return mb_strtoupper(mb_substr($mots[0], 0, 1).mb_substr($mots[count($mots) - 1], 0, 1));
    }

    public function pronostics(): HasMany
    {
        return $this->hasMany(Pronostic::class);
    }

    public function reponsesBonus(): HasMany
    {
        return $this->hasMany(ReponseBonus::class);
    }
}
