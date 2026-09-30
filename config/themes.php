<?php

/**
 * Personnalisation de la couleur principale du site.
 *
 * L'admin choisit une couleur libre (color picker). Les 11 nuances Tailwind
 * (--color-primary-50 → 950) sont générées à la volée à partir de cette couleur
 * par App\Support\Theme : éclaircissement vers le blanc pour les nuances claires,
 * assombrissement vers le noir pour les foncées. La couleur choisie = nuance 600
 * (celle des boutons).
 */
return [
    // Couleur par défaut (indigo-600, l'ancien thème).
    'default' => '#4f46e5',

    // Raccourcis proposés sous le sélecteur (libellé => hex).
    'presets' => [
        'Indigo' => '#4f46e5',
        'Bleu' => '#2563eb',
        'Ciel' => '#0284c7',
        'Émeraude' => '#059669',
        'Turquoise' => '#0d9488',
        'Rouge' => '#dc2626',
        'Rose' => '#e11d48',
        'Orange' => '#ea580c',
        'Ambre' => '#d97706',
        'Violet' => '#7c3aed',
    ],

    // Compatibilité : anciens choix stockés sous forme de nom de palette.
    'aliases' => [
        'indigo' => '#4f46e5',
        'blue' => '#2563eb',
        'sky' => '#0284c7',
        'emerald' => '#059669',
        'teal' => '#0d9488',
        'red' => '#dc2626',
        'rose' => '#e11d48',
        'orange' => '#ea580c',
        'amber' => '#d97706',
        'violet' => '#7c3aed',
    ],
];
