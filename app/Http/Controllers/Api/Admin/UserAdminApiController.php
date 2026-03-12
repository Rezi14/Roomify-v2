<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserAdminApiController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::with('role')->orderBy('id_role', 'asc')->get();

        return response()->json(['users' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'id_role' => ['required', 'exists:roles,id_role'],
        ]);

        // Proteksi: tidak boleh buat admin (sama dengan UserController)
        $roleDipilih = Role::find($request->id_role);
        if ($roleDipilih && $roleDipilih->nama_role === 'admin') {
            return response()->json([
                'message' => 'Anda tidak diizinkan membuat user dengan role Admin.',
            ], 403);
        }

        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);
        $user->load('role');

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan!',
            'user' => $user,
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        $user->load('role');

        return response()->json(['user' => $user]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'id_role' => ['required', 'exists:roles,id_role'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        $validatedData = $request->validate($rules);

        // Proteksi role admin (sama dengan UserController)
        $roleDipilih = Role::find($request->id_role);
        if ($user->role->nama_role !== 'admin' && $roleDipilih && $roleDipilih->nama_role === 'admin') {
            return response()->json([
                'message' => 'Tidak bisa mengubah role menjadi Admin.',
            ], 403);
        }

        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui!',
            'user' => $user->fresh()->load('role'),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->hasRole('admin')) {
            return response()->json([
                'message' => 'User admin tidak bisa dihapus.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Pengguna berhasil dihapus!',
        ]);
    }
}
