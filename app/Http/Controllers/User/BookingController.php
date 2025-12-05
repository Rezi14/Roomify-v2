<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Pemesanan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * @method void middleware(string|array $middleware, array $options = [])
 */
class BookingController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function showBookingForm(Kamar $kamar): View|RedirectResponse
    {
        // Cek pending booking
        $pendingBooking = Pemesanan::where('user_id', Auth::id())
            ->where('status_pemesanan', 'pending')
            ->first();

        if ($pendingBooking) {
            return redirect()->route('booking.payment', $pendingBooking->id_pemesanan)
                ->with('error', 'Anda masih memiliki pesanan yang belum diselesaikan.');
        }

        $kamar->load('tipeKamar');
        $maxTamu = $kamar->tipeKamar->kapasitas;
        $fasilitasTersedia = Fasilitas::where('biaya_tambahan', '>', 0)->get();

        return view('user.booking', compact('kamar', 'fasilitasTersedia', 'maxTamu'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Cek pending booking
        $pendingBooking = Pemesanan::where('user_id', Auth::id())
            ->where('status_pemesanan', 'pending')
            ->first();

        if ($pendingBooking) {
            return redirect()->route('booking.payment', $pendingBooking->id_pemesanan)
                ->with('error', 'Selesaikan pembayaran transaksi sebelumnya.');
        }

        $request->validate([
            'kamar_id'       => ['required', 'exists:kamars,id_kamar'],
            'check_in_date'  => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'jumlah_tamu'    => ['required', 'integer', 'min:1'],
            'fasilitas_ids'  => ['nullable', 'array'],
        ]);

        $checkIn  = $request->check_in_date;
        $checkOut = $request->check_out_date;

        try {
            return DB::transaction(function () use ($request, $checkIn, $checkOut) {
                // (Validasi overlap tanggal TETAP SAMA)
                $isBooked = Pemesanan::where('kamar_id', $request->kamar_id)
                    ->where('status_pemesanan', '!=', 'cancelled')
                    ->where('status_pemesanan', '!=', 'checked_out')
                    ->where(function ($query) use ($checkIn, $checkOut) {
                        $query->where(function ($q) use ($checkIn, $checkOut) {
                            $q->where('check_in_date', '<', $checkOut)
                              ->where('check_out_date', '>', $checkIn);
                        });
                    })
                    ->lockForUpdate()
                    ->exists();

                if ($isBooked) {
                    return back()->with('error', 'Maaf, kamar tidak tersedia pada tanggal yang dipilih.');
                }

                $kamar = Kamar::with('tipeKamar')->findOrFail($request->kamar_id);

                // Validasi Tanggal & Durasi
                $in     = Carbon::parse($checkIn)->startOfDay();
                $out    = Carbon::parse($checkOut)->startOfDay();
                $durasi = $in->diffInDays($out);

                $totalHarga = $kamar->tipeKamar->harga_per_malam * $durasi;

                // Menyiapkan data fasilitas untuk pivot (agar harga tersimpan)
                $fasilitasData = [];
                if ($request->has('fasilitas_ids')) {
                    $fasilitas = Fasilitas::whereIn('id_fasilitas', $request->fasilitas_ids)->get();

                    foreach ($fasilitas as $f) {
                        $totalHarga += $f->biaya_tambahan;

                        // Menyiapkan array untuk attach() berisi harga saat ini
                        $fasilitasData[$f->id_fasilitas] = [
                            'jumlah' => 1,
                            'total_harga_fasilitas' => $f->biaya_tambahan
                        ];
                    }
                }

                $pemesanan = Pemesanan::create([
                    'user_id'          => Auth::id(),
                    'kamar_id'         => $kamar->id_kamar,
                    'check_in_date'    => $checkIn,
                    'check_out_date'   => $checkOut,
                    'jumlah_tamu'      => $request->jumlah_tamu,
                    'total_harga'      => $totalHarga,
                    'status_pemesanan' => 'pending',
                ]);

                // Attach dengan data pivot
                if (!empty($fasilitasData)) {
                    $pemesanan->fasilitas()->attach($fasilitasData);
                }

                return redirect()->route('booking.payment', $pemesanan->id_pemesanan)
                    ->with('success', 'Pesanan berhasil dibuat! Silakan bayar.');
            });

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function showPayment($id): View|RedirectResponse
    {
        $pemesanan = Pemesanan::with(['kamar.tipeKamar'])->findOrFail($id);

        if ($pemesanan->user_id !== Auth::id()) {
            abort(403);
        }

        // Jika sudah tidak pending, lempar ke dashboard
        if ($pemesanan->status_pemesanan !== 'pending') {
            return redirect()->route('dashboard');
        }

        // Menggunakan Method Model untuk Cek Expired
        if ($pemesanan->checkAndCancelIfExpired()) {
            return redirect()->route('dashboard')->with('error', 'Waktu pembayaran telah habis.');
        }

        // Menghitung sisa waktu untuk tampilan view
        $waktuDibuat = Carbon::parse($pemesanan->created_at);
        $batasWaktu  = $waktuDibuat->addMinutes(10);

        return view('user.payment', compact('pemesanan', 'batasWaktu'));
    }

    public function checkPaymentStatus($id): JsonResponse
    {
        $pemesanan = Pemesanan::findOrFail($id);

        if ($pemesanan->status_pemesanan == 'confirmed') {
            return response()->json(['status' => 'success']);
        }

        // Menggunakan Method Model untuk Cek Expired
        if ($pemesanan->checkAndCancelIfExpired()) {
            return response()->json(['status' => 'expired']);
        }

        if ($pemesanan->status_pemesanan == 'cancelled') {
            return response()->json(['status' => 'expired']);
        }

        return response()->json(['status' => 'pending']);
    }

    public function cancelBooking($id): RedirectResponse
    {
        $pemesanan = Pemesanan::findOrFail($id);

        if ($pemesanan->user_id == Auth::id() && $pemesanan->status_pemesanan == 'pending') {
            $pemesanan->update(['status_pemesanan' => 'cancelled']);
            return redirect()->route('dashboard')->with('success', 'Pesanan dibatalkan.');
        }

        return back();
    }

    public function detail($id): View
    {
        $pemesanan = Pemesanan::with(['kamar.tipeKamar', 'user', 'fasilitas'])->findOrFail($id);

        if (Auth::id() !== $pemesanan->user_id) {
            abort(403, 'ANDA TIDAK MEMILIKI AKSES KE HALAMAN INI.');
        }

        return view('user.pages.order-detail', compact('pemesanan'));
    }

    public function simulatePaymentSuccess($id): RedirectResponse
    {
        $pemesanan = Pemesanan::with('kamar')->findOrFail($id);

        if ($pemesanan->status_pemesanan == 'pending') {
            $pemesanan->update(['status_pemesanan' => 'confirmed']);
            return redirect()->route('dashboard')->with('success', 'Pembayaran Berhasil! Kamar Berhasil Dipesan.');
        }

        // Return Redirect Flash Message, bukan string
        return redirect()->route('dashboard')
            ->with('error', 'Pesanan tidak valid atau sudah diproses sebelumnya.');
    }
}
