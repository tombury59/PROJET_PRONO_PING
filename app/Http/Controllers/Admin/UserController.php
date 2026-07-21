<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $perPage = $request->cookie('viewport') === 'mobile' ? 5 : 10;

        $users = User::withCount(['pronostics', 'reponsesBonus'])
            ->when($search !== '', fn ($query) => $query->where('pseudo', 'like', '%'.$search.'%'))
            ->orderBy('pseudo')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users, 'search' => $search]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['admin', 'joueur'])],
        ]);

        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Tu ne peux pas modifier ton propre rôle.');
        }

        if ($user->isAdmin() && $data['role'] === 'joueur' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Impossible : il doit rester au moins un administrateur.');
        }

        $user->update(['role' => $data['role']]);

        return back()->with('status', 'Rôle mis à jour.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Tu ne peux pas supprimer ton propre compte depuis cette page.');
        }

        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'Impossible : il doit rester au moins un administrateur.');
        }

        $user->delete();

        return back()->with('status', 'Utilisateur supprimé.');
    }
}
