<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;

class AuthApiController extends Controller
{
    /**
     * Login - Memetakan LoginController@login
     * Mendukung login dengan email ATAU username (name), sesuai Blade login.
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email_or_name' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginField = $data['email_or_name'];
        $password = $data['password'];

        // Deteksi apakah input adalah email atau name (sama dengan LoginController)
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (!Auth::attempt([$fieldType => $loginField, 'password' => $password])) {
            return response()->json([
                'message' => 'Kombinasi email/nama pengguna dan password tidak valid.',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $user->load('role');

        // Buat token Sanctum
        $token = $user->createToken('roomify-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil! Selamat datang.',
            'user' => $user,
            'token' => $token,
        ]);

    }

    /**
     * Register - Memetakan RegisterController@register
     * Role default: pelanggan (menggunakan firstOrCreate, sama dengan aslinya)
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Sama dengan RegisterController - firstOrCreate role pelanggan
        $pelangganRole = Role::firstOrCreate(
            ['nama_role' => 'pelanggan'],
            ['deskripsi' => 'Pengguna biasa yang dapat memesan kamar.']
        );

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_role' => $pelangganRole->id_role,
        ]);

        $user->load('role');
        $token = $user->createToken('roomify-mobile')->plainTextToken;

        return response()->json([
            'message' => 'Pendaftaran berhasil! Selamat datang.',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Logout - Memetakan LoginController@logout
     * Menghapus token Sanctum saat ini.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Anda telah logout.',
        ]);
    }

    /**
     * Get User - Mendapatkan data user yang sedang login beserta role.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');

        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * Forgot Password - Memetakan ForgotPasswordController
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => 'Link reset password telah dikirim ke email Anda.',
            ]);
        }

        return response()->json([
            'message' => 'Gagal mengirim link reset password.',
        ], 400);
    }

    /**
     * Reset Password - Memetakan ResetPasswordController
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->update(['password' => Hash::make($password)]);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password berhasil direset.',
            ]);
        }

        return response()->json([
            'message' => 'Gagal mereset password.',
        ], 400);
    }
}
