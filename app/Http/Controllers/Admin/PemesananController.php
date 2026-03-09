<?php

namespace App\Http\Controllers\Admin;

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
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PemesananController extends Controller
{
    // Menampilkan daftar semua pemesanan aktif (selain paid/cancelled).
    public function index(): View
    {
        $pemesanans = Pemesanan::orderBy('id_pemesanan', 'asc')->get();

        return view('admin.pemesanans.index', compact('pemesanans'));
    }

    // Menampilkan formulir untuk membuat pemesanan baru.
    public function create(): View
    {
        $users = User::where('id_role', '!=', 1)->get();

        // Menampilkan semua kamar yang status fisiknya 'Tersedia' (tidak rusak/maintenance)
        $kamars = Kamar::with('tipeKamar')
            ->where('status_kamar', 1)
            ->orderBy('nomor_kamar', 'asc')
            ->get();

        $fasilitas = Fasilitas::where('biaya_tambahan', '>', 0)->get();

        return view('admin.pemesanans.create', compact('users', 'kamars', 'fasilitas'));
    }

    // Menyimpan pemesanan baru ke database.
    public function store(StorePemesananRequest $request): RedirectResponse
    {
        try {
            // Menggunakan Transaction
            return DB::transaction(function () use ($request) {
                // Mengecek Ketersediaan Kamar
                $checkIn  = $request->input('check_in_date');
                $checkOut = $request->input('check_out_date');
                $kamarId  = $request->input('kamar_id');

                $isBooked = Pemesanan::overlapping($kamarId, $checkIn, $checkOut)
                    ->lockForUpdate()
                    ->exists();

                if ($isBooked) {
                    return redirect()->back()
                        ->with('error', 'Kamar sudah terisi pada tanggal yang dipilih! Silakan pilih kamar atau tanggal lain.')
                        ->withInput();
                }

                // Menentukan User ID
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

                // Menyimpan Pemesanan
                $pemesanan = Pemesanan::create([
                    'user_id'          => $userId,
                    'kamar_id'         => $kamarId,
                    'check_in_date'    => $checkIn,
                    'check_out_date'   => $checkOut,
                    'jumlah_tamu'      => $request->input('jumlah_tamu'),
                    'total_harga'      => $request->input('total_harga'),
                    'status_pemesanan' => StatusPemesanan::from($request->input('status_pemesanan')),
                ]);

                // Menyimpan Fasilitas dengan Harga & Jumlah (Pivot)
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

                return redirect()->route('admin.pemesanans.index')
                    ->with('success', 'Pemesanan berhasil ditambahkan!');
            });

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Admin store pemesanan error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.')->withInput();
        }
    }

    // Menampilkan detail pemesanan tertentu.
    public function show(Pemesanan $pemesanan): View
    {
        $pemesanan->load(['user', 'kamar.tipeKamar', 'fasilitas']);
        return view('admin.pemesanans.show', compact('pemesanan'));
    }

    // Menampilkan formulir untuk mengedit pemesanan.
    public function edit(Pemesanan $pemesanan): View
    {
        $users = User::all();
        $kamars = Kamar::with('tipeKamar')->where('status_kamar', 1)->get();
        $fasilitas = Fasilitas::where('biaya_tambahan', '>', 0)->get();
        $selectedFasilitas = $pemesanan->fasilitas->pluck('id_fasilitas')->toArray();

        return view('admin.pemesanans.edit', compact('pemesanan', 'users', 'kamars', 'fasilitas', 'selectedFasilitas'));
    }

    // Memperbarui pemesanan di database.
    public function update(UpdatePemesananRequest $request, Pemesanan $pemesanan): RedirectResponse
    {
        try {
            // Menggunakan Transaction
            return DB::transaction(function () use ($request, $pemesanan) {
                $checkIn  = $request->input('check_in_date');
                $checkOut = $request->input('check_out_date');
                $kamarId  = $request->input('kamar_id');

                // Mengecek Bentrok
                $isBooked = Pemesanan::overlapping($kamarId, $checkIn, $checkOut, $pemesanan->id_pemesanan)
                    ->lockForUpdate()
                    ->exists();

                if ($isBooked) {
                    return redirect()->back()
                        ->with('error', 'Gagal update: Kamar sudah terisi pada tanggal tersebut.')
                        ->withInput();
                }

                // Logika Penentuan Harga
                // Jika Admin mengisi input 'total_harga' secara manual, akan digunakan
                // Jika kosong, akan menghitung ulang otomatis.
                if ($request->filled('total_harga')) {
                    $finalTotalHarga = $request->input('total_harga');

                    // Kita tetap perlu ambil objek fasilitas untuk sinkronisasi pivot, meski harganya manual
                    $selectedFasilitasIds = $request->input('fasilitas_tambahan', []);
                    $fasilitasObjs = Fasilitas::whereIn('id_fasilitas', $selectedFasilitasIds)->get();

                } else {
                    // Menghitung Ulang Otomatis
                    $selectedFasilitasIds = $request->input('fasilitas_tambahan', []);
                    $fasilitasObjs = Fasilitas::whereIn('id_fasilitas', $selectedFasilitasIds)->get();
                    $biayaTambahanTotal = $fasilitasObjs->sum('biaya_tambahan');

                    $kamar = Kamar::findOrFail($kamarId);
                    $hargaPerMalam = $kamar->tipeKamar->harga_per_malam;

                    $cIn = Carbon::parse($checkIn);
                    $cOut = Carbon::parse($checkOut);
                    $diffDays = $cIn->diffInDays($cOut);
                    if ($diffDays == 0)
                        $diffDays = 1;

                    $hargaKamarTotal = $hargaPerMalam * $diffDays;
                    $finalTotalHarga = $hargaKamarTotal + $biayaTambahanTotal;
                }

                // Update Data Pemesanan
                $pemesanan->update([
                    'user_id'          => $request->input('user_id'),
                    'kamar_id'         => $kamarId,
                    'check_in_date'    => $checkIn,
                    'check_out_date'   => $checkOut,
                    'jumlah_tamu'      => $request->input('jumlah_tamu'),
                    'total_harga'      => $finalTotalHarga,
                    'status_pemesanan' => StatusPemesanan::from($request->input('status_pemesanan')),
                ]);

                // Sync Fasilitas dengan Data Pivot
                $pivotData = [];
                foreach ($fasilitasObjs as $f) {
                    $pivotData[$f->id_fasilitas] = [
                        'jumlah'                => 1,
                        'total_harga_fasilitas' => $f->biaya_tambahan,
                    ];
                }
                $pemesanan->fasilitas()->sync($pivotData);

                return redirect()->route('admin.pemesanans.index')
                    ->with('success', 'Pemesanan berhasil diperbarui!');
            });

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Admin update pemesanan error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.')->withInput();
        }
    }

    public function confirm(Pemesanan $pemesanan): RedirectResponse
    {
        if ($pemesanan->status_pemesanan === StatusPemesanan::PENDING) {
            $pemesanan->update(['status_pemesanan' => StatusPemesanan::CONFIRMED]);
            return redirect()->back()->with('success', 'Pemesanan dikonfirmasi!');
        }
        return redirect()->back()->with('error', 'Hanya status Pending yang bisa dikonfirmasi.');
    }

    public function checkIn(Pemesanan $pemesanan): RedirectResponse
    {
        if ($pemesanan->status_pemesanan === StatusPemesanan::CONFIRMED) {
            $pemesanan->update(['status_pemesanan' => StatusPemesanan::CHECKED_IN]);
            return redirect()->back()->with('success', 'Tamu berhasil Check-In!');
        }
        return redirect()->back()->with('error', 'Harus Confirmed sebelum Check-In.');
    }

    // Mengubah status pemesanan menjadi 'paid'
    public function checkout(Pemesanan $pemesanan): RedirectResponse
    {
        try {
            if ($pemesanan->status_pemesanan === StatusPemesanan::CHECKED_IN) {

                // Mengaktifkan update check_out_date
                // Jika checkout lebih awal, update tanggal check_out_date agar kamar bisa dipakai besoknya
                $pemesanan->check_out_date = Carbon::now();

                $pemesanan->status_pemesanan = StatusPemesanan::PAID;
                $pemesanan->save();

                return redirect()->route('admin.dashboard')
                    ->with('success', 'Check out berhasil. Transaksi selesai.');
            }

            return redirect()->back()->with('error', 'Pemesanan belum Check-In.');
        } catch (\Exception $e) {
            Log::error('Admin checkout error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    // Menampilkan Riwayat pemesanan
    public function riwayat(): View
    {
        $riwayatPemesanan = Pemesanan::with(['user', 'kamar.tipeKamar'])
            ->whereIn('status_pemesanan', [StatusPemesanan::PAID, StatusPemesanan::CANCELLED])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.riwayat.pemesanan', compact('riwayatPemesanan'));
    }

    // Menampilkan Detail Riwayat Pemesanan
    public function detailRiwayat($id)
    {
        $pemesanan = Pemesanan::with(['user', 'kamar.tipeKamar', 'fasilitas'])->findOrFail($id);
        if (!in_array($pemesanan->status_pemesanan, [StatusPemesanan::PAID, StatusPemesanan::CANCELLED])) {
            return redirect()->route('admin.pemesanans.index')->with('error', 'Bukan data riwayat.');
        }
        return view('admin.riwayat.detail', compact('pemesanan'));
    }

    // Menghapus Pemesanan
    public function destroy(Pemesanan $pemesanan): RedirectResponse
    {
        try {
            if ($pemesanan->status_pemesanan === StatusPemesanan::PAID) {
                return redirect()->back()->with('error', 'Transaksi lunas (Paid) tidak boleh dihapus demi arsip keuangan.');
            }

            if (in_array($pemesanan->status_pemesanan, [StatusPemesanan::CONFIRMED, StatusPemesanan::CHECKED_IN])) {
                $today = Carbon::now()->startOfDay();
                $checkOut = Carbon::parse($pemesanan->check_out_date)->startOfDay();

                if ($today->lt($checkOut)) {
                    return redirect()->back()->with('error', 'Pesanan sedang berjalan/aktif. Lakukan Checkout atau Cancel terlebih dahulu.');
                }
            }

            $pemesanan->fasilitas()->detach();
            $pemesanan->delete();

            return redirect()->route('admin.pemesanans.index')
                ->with('success', 'Data pemesanan berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error('Admin destroy pemesanan error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }
}
