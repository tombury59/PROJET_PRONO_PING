<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PhaseController extends Controller
{
    public function index(): View
    {
        $phases = Phase::withCount('matches')->orderByDesc('date_debut')->get();

        return view('admin.phases.index', ['phases' => $phases]);
    }

    public function create(): View
    {
        return view('admin.phases.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        Phase::create($data);

        return redirect()->route('admin.phases.index')->with('status', 'Phase créée.');
    }

    public function edit(Phase $phase): View
    {
        return view('admin.phases.edit', ['phase' => $phase]);
    }

    public function update(Request $request, Phase $phase): RedirectResponse
    {
        $data = $this->validateData($request);

        $phase->update($data);

        return redirect()->route('admin.phases.index')->with('status', 'Phase mise à jour.');
    }

    public function destroy(Phase $phase): RedirectResponse
    {
        if ($phase->matches()->exists()) {
            return redirect()->route('admin.phases.index')
                ->with('error', 'Impossible de supprimer une phase qui contient des matchs.');
        }

        $phase->delete();

        return redirect()->route('admin.phases.index')->with('status', 'Phase supprimée.');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
        ]);

        $data['reset_classement'] = $request->boolean('reset_classement');

        return $data;
    }
}
