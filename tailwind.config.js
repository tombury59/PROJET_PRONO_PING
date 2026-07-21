import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';
import forms from '@tailwindcss/forms';

/**
 * Thème de l'application — seul endroit à modifier pour changer
 * la police ou la palette de couleurs, partout dans l'app.
 *
 * - `fontFamily` : nom de la police (garder en phase avec le lien
 *   chargé dans resources/views/layouts/partials/fonts.blade.php).
 * - `colors` : chaque clé est un alias sémantique (primary, success...)
 *   utilisé dans les vues (ex: `text-primary-600`, `bg-danger-100`).
 *   Pour changer de couleur, remplacer la palette Tailwind associée
 *   (ex: `primary: colors.blue`) ou fournir une palette personnalisée
 *   avec les mêmes nuances 50 → 950.
 */
const theme = {
    fontFamily: 'Figtree',
    colors: {
        primary: colors.indigo,
        surface: colors.neutral,
        success: colors.green,
        warning: colors.amber,
        danger: colors.red,
    },
};

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: [theme.fontFamily, ...defaultTheme.fontFamily.sans],
            },
            colors: theme.colors,
        },
    },

    plugins: [forms],
};
