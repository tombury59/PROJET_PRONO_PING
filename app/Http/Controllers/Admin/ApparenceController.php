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
    public function edit(): View
    {
        return view('admin.apparence.edit', [
            'palettes' => Theme::palettes(),
            'couleurActuelle' => Theme::current(),
            'logoPath' => Setting::get('logo_path'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'primary_color' => ['required', 'string', 'in:'.implode(',', array_keys(Theme::palettes()))],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
            'supprimer_logo' => ['nullable', 'boolean'],
        ]);

        Setting::set('primary_color', $validated['primary_color']);

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
