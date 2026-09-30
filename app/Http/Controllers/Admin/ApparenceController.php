<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ApparenceController extends Controller
{
    /** Zones dont la couleur est réglable indépendamment. */
    private const ZONES = ['color_header', 'color_navbar', 'color_page'];

    public function edit(): View
    {
        return view('admin.apparence.edit', [
            'presets' => Theme::presets(),
            'couleurBouton' => Theme::primary(),
            'couleurHeader' => Theme::headerColor(),
            'couleurNavbar' => Theme::navbarColor(),
            'couleurFond' => Theme::pageColor(),
            'logoPath' => Setting::get('logo_path'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $hex = 'regex:/^#[0-9a-fA-F]{6}$/';

        $validated = $request->validate([
            'primary_color' => ['required', 'string', $hex],
            'color_header' => ['nullable', 'string', $hex],
            'color_navbar' => ['nullable', 'string', $hex],
            'color_page' => ['nullable', 'string', $hex],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'supprimer_logo' => ['nullable', 'boolean'],
        ], [
            'primary_color.regex' => 'La couleur doit être un code hexadécimal valide (ex. #4f46e5).',
            'color_header.regex' => 'La couleur doit être un code hexadécimal valide.',
            'color_navbar.regex' => 'La couleur doit être un code hexadécimal valide.',
            'color_page.regex' => 'La couleur doit être un code hexadécimal valide.',
        ]);

        Setting::set('primary_color', $validated['primary_color']);

        foreach (self::ZONES as $zone) {
            if ($request->has($zone)) {
                Setting::set($zone, $validated[$zone] ?? null);
            }
        }

        $logoPath = Setting::get('logo_path');

        if ($request->boolean('supprimer_logo') && $logoPath) {
            Storage::disk('public')->delete($logoPath);
            Setting::set('logo_path', null);
        }

        if ($request->hasFile('logo')) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            $nouveau = $request->file('logo')->store('branding', 'public');
            Setting::set('logo_path', $nouveau);
        }

        return redirect()->route('admin.apparence.edit')->with('status', 'apparence-updated');
    }
}
