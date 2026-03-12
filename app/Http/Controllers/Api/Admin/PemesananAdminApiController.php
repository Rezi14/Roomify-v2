<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\StatusPemesanan;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePemesananRequest;
use App\Http\Requests\UpdatePemesananRequest;
use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PemesananAdminApiController extends Controller
{
    /**
     * Memetakan PemesananController@index
     */
    public function index(): JsonResponse
    {
        $pemesanans = Pemesanan::with(['user', 'kamar.tipeKamar', 'fasilitas'])
            ->orderBy('id_pemesanan', 'asc')
            ->get();

        return response()->json(['pemesanans' => $pemesanans]);
    }

    /**
     * Memetakan PemesananController@create
     * Mengembalikan data yang dibutuhkan form (users, kamars, fasilitas).
     */
    public function create(): JsonResponse
    {
        $users = User::where('id_role', '!=', 1)->get();
        $kamars = Kamar::with('tipeKamar')
            ->where('status_kamar', 1)
            ->orderBy('nomor_kamar', 'asc')
            ->get();
        $fasilitas = Fasilitas::where('biaya_tambahan', '>', 0)->get();

        return response()->json([
            'users' => $users,
            'kamars' => $kamars,
            'fasilitas' => $fasilitas,
        ]);
    }

    /**
     * Memetakan PemesananController@store
     * Menggunakan StorePemesananRequest yang sudah ada.
     */
    public function store(StorePemesananRequest $request): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request): JsonResponse {
                $checkIn  = $request->input('check_in_date');
                $checkOut = $request->input('check_out_date');
                $kamarId  = $request->input('kamar_id');

                // Cek ketersediaan (sama dengan PemesananController)
                $isBooked = Pemesanan::overlapping($kamarId, $checkIn, $checkOut)
                    ->lockForUpdate()
                    ->exists();

                if ($isBooked) {
                    return response()->json([
                        'message' => 'Kamar sudah terisi pada tanggal yang dipilih!',
                    ], 422);
                }

                // Menentukan User ID (sama dengan PemesananController)
                $userId = null;
                if ($request->input('customer_type') === 'new') {
                    $customerRole = Role::where('nama_role', 'customer')->first();
                    $roleId = $customerRole ? $customerRole->id_role : 2;

                    $newUser = User::create([
                        'name'     => $request->input('new_user_name'),
                        'email'    => $request->input('new_user_email'),
                        'password' => Hash::make('password123'),
                        'id_role'  => $roleId,
                    ]);
                    $userId = $newUser->id;
                } else {
                    $userId = $request->input('user_id');
                }

                // Simpan Pemesanan
                $pemesanan = Pemesanan::create([
                    'user_id'          => $userId,
                    'kamar_id'         => $kamarId,
                    'check_in_date'    => $checkIn,
                    'check_out_date'   => $checkOut,
                    'jumlah_tamu'      => $request->input('jumlah_tamu'),
                    'total_harga'      => $request->input('total_harga'),
                    'status_pemesanan' => StatusPemesanan::from($request->input('status_pemesanan')),
                ]);

                // Simpan fasilitas dengan pivot (sama dengan PemesananController)
                if ($request->has('fasilitas_tambahan')) {
                    $fasilitasIds  = $request->input('fasilitas_tambahan');
                    $fasilitasObjs = Fasilitas::whereIn('id_fasilitas', $fasilitasIds)->get();

                    $pivotData = [];
                    foreach ($fasilitasObjs as $f) {
                        $pivotData[$f->id_fasilitas] = [
                            'jumlah'                => 1,
                            'total_harga_fasilitas' => $f->biaya_tambahan,
                        ];
                    }

                    if (!empty($pivotData)) {
                        $pemesanan->fasilitas()->attach($pivotData);
                    }
                }

                $pemesanan->load(['user', 'kamar.tipeKamar', 'fasilitas']);

                return response()->json([
                    'message' => 'Pemesanan berhasil ditambahkan!',
                    'pemesanan' => $pemesanan,
                ], 201);
            });

        } catch (\Exception $e) {
            Log::error('API Admin store pemesanan error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Terjadi kesalahan sistem.',
            ], 500);
        }
    }

    /**
     * Memetakan PemesananController@show
     */
    public function show(Pemesanan $pemesanan): JsonResponse
    {
        $pemesanan->load(['user', 'kamar.tipeKamar', 'fasilitas']);

        return response()->json(['pemesanan' => $pemesanan]);
    }

    /**
     * Memetakan PemesananController@edit
     * Mengembalikan data pemesanan + data form untuk edit.
     */
    public function edit(Pemesanan $pemesanan): JsonResponse
    {
        $pemesanan->load(['user', 'kamar.tipeKamar', 'fasilitas']);

        $users = User::all();
        $kamars = Kamar::with('tipeKamar')->where('status_kamar', 1)->get();
        $fasilitas = Fasilitas::where('biaya_tambahan', '>', 0)->get();
        $selectedFasilitas = $pemesanan->fasilitas->pluck('id_fasilitas')->toArray();

        return response()->json([
            'pemesanan' => $pemesanan,
            'users' => $users,
            'kamars' => $kamars,
            'fasilitas' => $fasilitas,
            'selected_fasilitas' => $selectedFasilitas,
        ]);
    }

    /**
     * Memetakan PemesananController@update
     * Menggunakan UpdatePemesananRequest yang sudah ada.
     */
    public function update(UpdatePemesananRequest $request, Pemesanan $pemesanan): JsonResponse
    {
        try {
            return DB::transaction(function () use ($request, $pemesanan): JsonResponse {
                $checkIn  = $request->input('check_in_date');
                $checkOut = $request->input('check_out_date');
                $kamarId  = $request->input('kamar_id');

                // Cek bentrok (sama dengan PemesananController)
                $isBooked = Pemesanan::overlapping($kamarId, $checkIn, $checkOut, $pemesanan->id_pemesanan)
                    ->lockForUpdate()
                    ->exists();

                if ($isBooked) {
                    return response()->json([
                        'message' => 'Gagal update: Kamar sudah terisi pada tanggal tersebut.',
                    ], 422);
                }

                // Logika penentuan harga (sama dengan PemesananController)
                if ($request->filled('total_harga')) {
                    $finalTotalHarga = $request->input('total_harga');
                    $selectedFasilitasIds = $request->input('fasilitas_tambahan', []);
                    $fasilitasObjs = Fasilitas::whereIn('id_fasilitas', $selectedFasilitasIds)->get();
                } else {
                    $selectedFasilitasIds = $request->input('fasilitas_tambahan', []);
                    $fasilitasObjs = Fasilitas::whereIn('id_fasilitas', $selectedFasilitasIds)->get();
                    $biayaTambahanTotal = $fasilitasObjs->sum('biaya_tambahan');

                    $kamar = Kamar::findOrFail($kamarId);
                    $hargaPerMalam = $kamar->tipeKamar->harga_per_malam;

                    $cIn = Carbon::parse($checkIn);
                    $cOut = Carbon::parse($checkOut);
                    $diffDays = $cIn->diffInDays($cOut);
                    if ($diffDays == 0) $diffDays = 1;

                    $hargaKamarTotal = $hargaPerMalam * $diffDays;
                    $finalTotalHarga = $hargaKamarTotal + $biayaTambahanTotal;
                }

                $pemesanan->update([
                    'user_id'          => $request->input('user_id'),
                    'kamar_id'         => $kamarId,
                    'check_in_date'    => $checkIn,
                    'check_out_date'   => $checkOut,
                    'jumlah_tamu'      => $request->input('jumlah_tamu'),
                    'total_harga'      => $finalTotalHarga,
                    'status_pemesanan' => StatusPemesanan::from($request->input('status_pemesanan')),
                ]);

                // Sync fasilitas (sama dengan PemesananController)
                $pivotData = [];
                foreach ($fasilitasObjs as $f) {
                    $pivotData[$f->id_fasilitas] = [
                        'jumlah'                => 1,
                        'total_harga_fasilitas' => $f->biaya_tambahan,
                    ];
                }
                $pemesanan->fasilitas()->sync($pivotData);

                return response()->json([
                    'message' => 'Pemesanan berhasil diperbarui!',
                    'pemesanan' => $pemesanan->fresh()->load(['user', 'kamar.tipeKamar', 'fasilitas']),
                ]);
            });

        } catch (\Exception $e) {
            Log::error('API Admin update pemesanan error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Terjadi kesalahan sistem.',
            ], 500);
        }
    }

    /**
     * Memetakan PemesananController@confirm
     */
    public function confirm(Pemesanan $pemesanan): JsonResponse
    {
        if ($pemesanan->status_pemesanan === StatusPemesanan::PENDING) {
            $pemesanan->update(['status_pemesanan' => StatusPemesanan::CONFIRMED]);
            return response()->json(['message' => 'Pemesanan dikonfirmasi!', 'pemesanan' => $pemesanan->fresh()]);
        }

        return response()->json(['message' => 'Hanya status Pending yang bisa dikonfirmasi.'], 422);
    }

    /**
     * Memetakan PemesananController@checkIn
     */
    public function checkIn(Pemesanan $pemesanan): JsonResponse
    {
        if ($pemesanan->status_pemesanan === StatusPemesanan::CONFIRMED) {
            $pemesanan->update(['status_pemesanan' => StatusPemesanan::CHECKED_IN]);
            return response()->json(['message' => 'Tamu berhasil Check-In!', 'pemesanan' => $pemesanan->fresh()]);
        }

        return response()->json(['message' => 'Harus Confirmed sebelum Check-In.'], 422);
    }

    /**
     * Memetakan PemesananController@checkout
     */
    public function checkout(Pemesanan $pemesanan): JsonResponse
    {
        try {
            if ($pemesanan->status_pemesanan === StatusPemesanan::CHECKED_IN) {
                $pemesanan->check_out_date = Carbon::now();
                $pemesanan->status_pemesanan = StatusPemesanan::PAID;
                $pemesanan->save();

                return response()->json([
                    'message' => 'Check out berhasil. Transaksi selesai.',
                    'pemesanan' => $pemesanan->fresh(),
                ]);
            }

            return response()->json(['message' => 'Pemesanan belum Check-In.'], 422);

        } catch (\Exception $e) {
            Log::error('API Admin checkout error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Memetakan PemesananController@riwayat
     */
    public function riwayat(): JsonResponse
    {
        $riwayatPemesanan = Pemesanan::with(['user', 'kamar.tipeKamar'])
            ->whereIn('status_pemesanan', [StatusPemesanan::PAID, StatusPemesanan::CANCELLED])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json(['riwayat' => $riwayatPemesanan]);
    }

    /**
     * Memetakan PemesananController@detailRiwayat
     */
    public function detailRiwayat(int $id): JsonResponse
    {
        $pemesanan = Pemesanan::with(['user', 'kamar.tipeKamar', 'fasilitas'])->findOrFail($id);

        if (!in_array($pemesanan->status_pemesanan, [StatusPemesanan::PAID, StatusPemesanan::CANCELLED])) {
            return response()->json(['message' => 'Bukan data riwayat.'], 422);
        }

        return response()->json(['pemesanan' => $pemesanan]);
    }

    /**
     * Memetakan PemesananController@destroy
     */
    public function destroy(Pemesanan $pemesanan): JsonResponse
    {
        try {
            if ($pemesanan->status_pemesanan === StatusPemesanan::PAID) {
                return response()->json([
                    'message' => 'Transaksi lunas (Paid) tidak boleh dihapus demi arsip keuangan.',
                ], 422);
            }

            if (in_array($pemesanan->status_pemesanan, [StatusPemesanan::CONFIRMED, StatusPemesanan::CHECKED_IN])) {
                $today = Carbon::now()->startOfDay();
                $checkOut = Carbon::parse($pemesanan->check_out_date)->startOfDay();

                if ($today->lt($checkOut)) {
                    return response()->json([
                        'message' => 'Pesanan sedang berjalan/aktif. Lakukan Checkout atau Cancel terlebih dahulu.',
                    ], 422);
                }
            }

            $pemesanan->fasilitas()->detach();
            $pemesanan->delete();

            return response()->json([
                'message' => 'Data pemesanan berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            Log::error('API Admin destroy pemesanan error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }
}
