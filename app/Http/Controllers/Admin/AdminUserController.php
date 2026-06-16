<?php

namespace App\Http\Controllers\Admin;

use App\Services\AuditLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends AdminController
{
    public function index(Request $request)
    {
        $query = User::withCount('signalements')->orderByDesc('created_at');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'users' => $users,
            'filters' => $request->only(['q', 'role']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in([User::ROLE_CITOYEN, User::ROLE_ADMIN])],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        AuditLogger::log('user.create', $user);

        return back()->with('success', 'Utilisateur créé avec succès.');
    }

    public function show(User $user)
    {
        $signalements = $user->signalements()
            ->with('category')
            ->orderByDesc('reported_at')
            ->get()
            ->map->toViewArray();

        return view('admin.users.show', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'profile' => $user,
            'signalements' => $signalements,
        ]);
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === $this->adminUser()->id && $request->role !== User::ROLE_ADMIN) {
            return back()->withErrors(['role' => 'Vous ne pouvez pas retirer votre propre rôle administrateur.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in([User::ROLE_CITOYEN, User::ROLE_ADMIN])],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLogger::log('user.update', $user);

        return back()->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user)
    {
        if ($user->id === $this->adminUser()->id) {
            return back()->withErrors(['delete' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        if ($user->signalements()->exists()) {
            return back()->withErrors(['delete' => 'Impossible de supprimer un utilisateur avec des signalements.']);
        }

        AuditLogger::log('user.delete', $user);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }
}
