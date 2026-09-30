<?php

namespace App\Support;

use App\Models\Setting;

class Theme
{
    /**
     * Toutes les palettes disponibles (clé => ['label' => ..., 'shades' => [...]]).
     */
    public static function palettes(): array
    {
        return config('themes.palettes', []);
    }

    public static function default(): string
    {
        return config('themes.default', 'indigo');
    }

    /**
     * Palette actuellement sélectionnée (retombe sur le défaut si invalide).
     */
    public static function current(): string
    {
        $choice = Setting::get('primary_color', static::default());

        return array_key_exists($choice, static::palettes()) ? $choice : static::default();
    }

    /**
     * Bloc `:root` définissant les variables CSS de la couleur primaire.
     */
    public static function cssVariables(): string
    {
        $shades = static::palettes()[static::current()]['shades'] ?? [];

        $lines = '';
        foreach ($shades as $shade => $hex) {
            [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
            $lines .= "--color-primary-{$shade}:{$r} {$g} {$b};";
        }

        return ":root{{$lines}}";
    }
}
