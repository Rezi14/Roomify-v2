<?php

namespace App\Http\Controllers\Api\User     ;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileApiController extends Controller
{
    /**
     * Memetakan ProfileController@index
     * Menampilkan data profil user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');

        return response()->json([
            'user' => $user,
        ]);
    }

    /**
     * Memetakan ProfileController@update
     * Mengupdate nama dan email.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $user->update($validatedData);

        return response()->json([
            'message' => 'Profil berhasil diperbarui!',
            'user' => $user->fresh()->load('role'),
        ]);
    }

    /**
     * Memetakan ProfileController@updatePassword
     * Mengganti password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        // Cek apakah password lama benar (sama dengan ProfileController)
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Password saat ini salah!',
                'errors' => ['current_password' => ['Password saat ini salah!']],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah!',
        ]);
    }

    /**
     * Memetakan data dari profile.blade.php - tabel riwayat pesanan.
     */
    public function orders(Request $request): JsonResponse
    {
        $orders = Pemesanan::with(['kamar.tipeKamar', 'fasilitas'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }

    /**
     * Memetakan BookingController@detail untuk halaman order-detail.blade.php.
     */
    public function orderDetail(int $id, Request $request): JsonResponse
    {
        $order = Pemesanan::with(['kamar.tipeKamar.fasilitas', 'fasilitas', 'user'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'order' => $order,
        ]);
    }
}
