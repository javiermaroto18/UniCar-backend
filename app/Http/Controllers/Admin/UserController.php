<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    // GET /admin/users — listado con búsqueda y paginación
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->withCount(['trips', 'bookings', 'vehicles'])
            ->orderByDesc('id') // los usuarios más recientes primero
            ->paginate(12)
            ->withQueryString()
            ->through(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'is_verified_driver' => $u->is_verified_driver,
                'is_admin' => $u->hasRole('admin'),
                'trips_count' => $u->trips_count,
                'bookings_count' => $u->bookings_count,
                'vehicles_count' => $u->vehicles_count,
                'created_at' => $u->created_at->toDateString(),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => ['search' => $search],
        ]);
    }

    // PATCH /admin/users/{user} — alternar verificación o rol admin
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'action' => ['required', 'in:toggle_verified,toggle_admin'],
        ]);

        if ($data['action'] === 'toggle_verified') {
            $user->update(['is_verified_driver' => ! $user->is_verified_driver]);

            return back()->with('success', 'Estado de conductor verificado actualizado.');
        }

        // toggle_admin: evitar que un admin se quite el rol a sí mismo
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'No puedes cambiar tu propio rol de administrador.');
        }

        $user->hasRole('admin')
            ? $user->removeRole('admin')
            : $user->assignRole('admin');

        return back()->with('success', 'Rol de administrador actualizado.');
    }

    // DELETE /admin/users/{user} — eliminar usuario
    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return back()->with('success', 'Usuario eliminado correctamente.');
    }
}
