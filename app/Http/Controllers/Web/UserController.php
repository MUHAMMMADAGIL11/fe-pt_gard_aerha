<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()?->hasRole(['KepalaDivisi', 'AdminGudang'])) {
            abort(403, 'Hanya Kepala Divisi atau Admin Gudang yang dapat mengelola user.');
        }
        $users = User::orderBy('role')
            ->orderBy('username')
            ->paginate(15);

        return view('pages.entities.user.index', compact('users'));
    }

    public function create()
    {
        $user = auth()->user();
        if (!$user?->hasRole(['KepalaDivisi', 'AdminGudang'])) {
            abort(403, 'Anda tidak memiliki akses untuk menambah user.');
        }
        $allowedRoles = $user->hasRole('KepalaDivisi')
            ? ['AdminGudang','PetugasOperasional','KepalaDivisi']
            : ['PetugasOperasional'];
        return view('pages.entities.user.create', compact('allowedRoles'));
    }

    public function store(Request $request)
    {
        $actor = auth()->user();
        if (!$actor?->hasRole(['KepalaDivisi', 'AdminGudang'])) {
            abort(403, 'Anda tidak memiliki akses untuk menambah user.');
        }
        $allowedRoles = $actor->hasRole('KepalaDivisi')
            ? ['AdminGudang','PetugasOperasional','KepalaDivisi']
            : ['PetugasOperasional'];
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'nama_lengkap' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:' . implode(',', $allowedRoles)],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'nama_lengkap' => $validated['nama_lengkap'] ?? null,
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('user.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(int $userId)
    {
        if (!auth()->user()?->hasRole('KepalaDivisi')) {
            abort(403, 'Hanya Kepala Divisi yang dapat mengelola user.');
        }
        $user = User::findOrFail($userId);

        return view('pages.entities.user.edit', compact('user'));
    }

    public function update(Request $request, int $userId)
    {
        if (!auth()->user()?->hasRole('KepalaDivisi')) {
            abort(403, 'Hanya Kepala Divisi yang dapat mengelola user.');
        }
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username,' . $userId . ',id_user'],
            'password' => ['nullable', 'string', 'min:6'],
            'nama_lengkap' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:AdminGudang,PetugasOperasional,KepalaDivisi'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $updateData = [
            'username' => $validated['username'],
            'nama_lengkap' => $validated['nama_lengkap'] ?? null,
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()
            ->route('user.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(int $userId)
    {
        if (!auth()->user()?->hasRole('KepalaDivisi')) {
            abort(403, 'Hanya Kepala Divisi yang dapat mengelola user.');
        }
        $user = User::findOrFail($userId);

        if ($user->id_user === auth()->user()->id_user) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()
            ->route('user.index')
            ->with('success', 'User berhasil dihapus.');
    }
}

