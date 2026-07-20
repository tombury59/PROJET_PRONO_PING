<?php

namespace App\Http\Controllers;

use App\Models\Phase;
use App\Services\ClassementService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassementController extends Controller
{
    public function index(Request $request, ClassementService $classementService): View
    {
        $phases = Phase::orderByDesc('date_debut')->get();
        $phaseCourante = Phase::courante();

        $selection = $request->input('vue', $phaseCourante?->id);

        if ($selection === 'global') {
            $phase = null;
            $classement = $classementService->global();
        } else {
            $phase = $phases->firstWhere('id', (int) $selection) ?? $phaseCourante;
            $classement = $phase ? $classementService->pourPhase($phase) : collect();
        }

        return view('classement.index', [
            'phases' => $phases,
            'phase' => $phase,
            'selection' => $selection,
            'classement' => $classement,
            'monRang' => $classementService->rangDe($request->user(), $classement),
        ]);
    }
}
