<?php

namespace App\Support;

use App\Models\Setting;

class Theme
{
    /**
     * Poids de mélange par nuance : 'w' = vers le blanc, 'k' = vers le noir,
     * null = couleur de base telle quelle (nuance 600).
     */
    private const STEPS = [
        50 => ['w', 0.90],
        100 => ['w', 0.80],
        200 => ['w', 0.64],
        300 => ['w', 0.46],
        400 => ['w', 0.26],
        500 => ['w', 0.12],
        600 => [null, 0.0],
        700 => ['k', 0.12],
        800 => ['k', 0.26],
        900 => ['k', 0.42],
        950 => ['k', 0.62],
    ];

    public static function default(): string
    {
        return config('themes.default', '#4f46e5');
    }

    /** Raccourcis proposés sous le sélecteur (libellé => hex). */
    public static function presets(): array
    {
        return config('themes.presets', []);
    }

    public static function isHex(?string $value): bool
    {
        return is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1;
    }

    /**
     * Couleur des boutons / accents (pilote la palette primary-*).
     * Résout les anciens noms de palette (ex. "red") et retombe sur le défaut.
     */
    public static function primary(): string
    {
        $value = Setting::get('primary_color', static::default());

        if (static::isHex($value)) {
            return strtolower($value);
        }

        return config('themes.aliases', [])[$value] ?? static::default();
    }

    /** Alias historique conservé pour les vues existantes. */
    public static function current(): string
    {
        return static::primary();
    }

    /**
     * Nuances générées à partir de la couleur des boutons.
     *
     * @return array<int, array{0:int,1:int,2:int}> shade => [r,g,b]
     */
    public static function primaryRgb(): array
    {
        [$r, $g, $b] = sscanf(static::primary(), '#%02x%02x%02x');

        $out = [];
        foreach (self::STEPS as $shade => [$dir, $weight]) {
            if ($dir === 'w') {
                $out[$shade] = [
                    (int) round($r + (255 - $r) * $weight),
                    (int) round($g + (255 - $g) * $weight),
                    (int) round($b + (255 - $b) * $weight),
                ];
            } elseif ($dir === 'k') {
                $out[$shade] = [
                    (int) round($r * (1 - $weight)),
                    (int) round($g * (1 - $weight)),
                    (int) round($b * (1 - $weight)),
                ];
            } else {
                $out[$shade] = [$r, $g, $b];
            }
        }

        return $out;
    }

    private static function shadeHex(int $shade): string
    {
        [$r, $g, $b] = static::primaryRgb()[$shade];

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    /** Couleur d'une zone (réglage libre) ou repli sur une nuance de la palette. */
    private static function zone(string $key, string $fallback): string
    {
        $value = Setting::get($key);

        return static::isHex($value) ? strtolower($value) : $fallback;
    }

    public static function pageColor(): string
    {
        return static::zone('color_page', static::shadeHex(50));
    }

    public static function headerColor(): string
    {
        return static::zone('color_header', static::shadeHex(100));
    }

    public static function navbarColor(): string
    {
        return static::zone('color_navbar', static::shadeHex(100));
    }

    /** Luminance perçue (0 = noir, 1 = blanc). */
    private static function luminance(string $hex): float
    {
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
    }

    /** Couleur de texte lisible (foncé ou blanc) sur un fond donné. */
    public static function foreground(string $hex): string
    {
        return static::luminance($hex) > 0.6 ? '#171717' : '#ffffff';
    }

    /** Overlay translucide basé sur la couleur de texte (bordures / survols). */
    private static function overlay(string $fgHex, float $alpha): string
    {
        [$r, $g, $b] = sscanf($fgHex, '#%02x%02x%02x');

        return "rgba($r, $g, $b, $alpha)";
    }

    /** Mélange une couleur vers le noir ($w = 0 inchangé, 1 = noir). */
    private static function mixBlack(string $hex, float $w): string
    {
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

        return sprintf(
            '#%02x%02x%02x',
            (int) round($r * (1 - $w)),
            (int) round($g * (1 - $w)),
            (int) round($b * (1 - $w)),
        );
    }

    /**
     * Variables de zones pour le mode sombre : teintes profondes dérivées de
     * la couleur des boutons, pour garder l'identité de marque en sombre.
     *
     * @return array<string, string>
     */
    private static function darkZoneVariables(): array
    {
        $primary = static::primary();

        $page = static::mixBlack($primary, 0.90);
        $header = static::mixBlack($primary, 0.82);
        $navbar = static::mixBlack($primary, 0.82);

        // Sur ces fonds sombres le texte est toujours clair.
        $fg = '#f5f5f5';

        return [
            '--zone-page' => $page,
            '--zone-header' => $header,
            '--zone-header-fg' => $fg,
            '--zone-header-border' => static::overlay($fg, 0.14),
            '--zone-navbar' => $navbar,
            '--zone-navbar-fg' => $fg,
            '--zone-navbar-border' => static::overlay($fg, 0.16),
            '--zone-navbar-hover' => static::overlay($fg, 0.12),
        ];
    }

    /**
     * Bloc `:root` : palette primary-* (r g b) + variables de zones.
     */
    public static function cssVariables(): string
    {
        $lines = '';

        // Palette des boutons / accents.
        foreach (static::primaryRgb() as $shade => [$r, $g, $b]) {
            $lines .= "--color-primary-{$shade}:{$r} {$g} {$b};";
        }

        // Zones indépendantes.
        $page = static::pageColor();
        $header = static::headerColor();
        $navbar = static::navbarColor();
        $navFg = static::foreground($navbar);
        $headerFg = static::foreground($header);

        $lines .= "--zone-page:{$page};";
        $lines .= "--zone-header:{$header};";
        $lines .= "--zone-header-fg:{$headerFg};";
        $lines .= '--zone-header-border:'.static::overlay($headerFg, 0.12).';';
        $lines .= "--zone-navbar:{$navbar};";
        $lines .= "--zone-navbar-fg:{$navFg};";
        $lines .= '--zone-navbar-border:'.static::overlay($navFg, 0.15).';';
        $lines .= '--zone-navbar-hover:'.static::overlay($navFg, 0.12).';';

        // Surcharges des zones en mode sombre (classe .dark sur <html>).
        $dark = '';
        foreach (static::darkZoneVariables() as $name => $value) {
            $dark .= "{$name}:{$value};";
        }

        return ":root{{$lines}}html.dark{{$dark}}";
    }
}
