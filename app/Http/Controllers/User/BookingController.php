<?php

namespace App\Http\Controllers\User;

use App\Enums\StatusPemesanan;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * @method void middleware(string|array $middleware, array $options = [])
 */
class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    public function showBookingForm(Kamar $kamar): View|RedirectResponse
    {
        // Cek pending booking
        $pendingBooking = Pemesanan::where('user_id', Auth::id())
            ->where('status_pemesanan', StatusPemesanan::PENDING)
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

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        // Cek pending booking
        $pendingBooking = Pemesanan::where('user_id', Auth::id())
            ->where('status_pemesanan', StatusPemesanan::PENDING)
            ->first();

        if ($pendingBooking) {
            return redirect()->route('booking.payment', $pendingBooking->id_pemesanan)
                ->with('error', 'Selesaikan pembayaran transaksi sebelumnya.');
        }

        $checkIn  = $request->check_in_date;
        $checkOut = $request->check_out_date;

        try {
            return DB::transaction(function () use ($request, $checkIn, $checkOut) {
                if (!$this->bookingService->isRoomAvailable((int) $request->kamar_id, $checkIn, $checkOut)) {
                    return back()->with('error', 'Maaf, kamar tidak tersedia pada tanggal yang dipilih.');
                }

                $kamar = Kamar::with('tipeKamar')->findOrFail($request->kamar_id);

                $fasilitasIds  = $request->input('fasilitas_ids', []);
                $totalHarga    = $this->bookingService->calculateTotalPrice($kamar, $checkIn, $checkOut, $fasilitasIds);
                $fasilitasData = $this->bookingService->prepareFasilitasPivotData($fasilitasIds);

                $pemesanan = Pemesanan::create([
                    'user_id'          => Auth::id(),
                    'kamar_id'         => $kamar->id_kamar,
                    'check_in_date'    => $checkIn,
                    'check_out_date'   => $checkOut,
                    'jumlah_tamu'      => $request->jumlah_tamu,
                    'total_harga'      => $totalHarga,
                    'status_pemesanan' => StatusPemesanan::PENDING,
                ]);

                // Attach dengan data pivot
                if (!empty($fasilitasData)) {
                    $pemesanan->fasilitas()->attach($fasilitasData);
                }

                return redirect()->route('booking.payment', $pemesanan->id_pemesanan)
                    ->with('success', 'Pesanan berhasil dibuat! Silakan bayar.');
            });

        } catch (\Exception $e) {
            Log::error('Booking error: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function showPayment($id): View|RedirectResponse
    {
        $pemesanan = Pemesanan::with(['kamar.tipeKamar'])->findOrFail($id);

        if ($pemesanan->user_id !== Auth::id()) {
            abort(403);
        }

        // Jika sudah tidak pending, lempar ke dashboard
        if ($pemesanan->status_pemesanan !== StatusPemesanan::PENDING) {
            return redirect()->route('dashboard');
        }

        // Menggunakan Method Model untuk Cek Expired
        if ($pemesanan->checkAndCancelIfExpired()) {
            return redirect()->route('dashboard')->with('error', 'Waktu pembayaran telah habis.');
        }

        // Menghitung sisa waktu untuk tampilan view
        $waktuDibuat = Carbon::parse($pemesanan->created_at);
        $batasWaktu  = $waktuDibuat->copy()->addMinutes(10);

        return view('user.payment', compact('pemesanan', 'batasWaktu'));
    }

    public function checkPaymentStatus($id): JsonResponse
    {
        $pemesanan = Pemesanan::findOrFail($id);

        if ($pemesanan->user_id !== Auth::id()) {
            abort(403);
        }

        if ($pemesanan->status_pemesanan === StatusPemesanan::CONFIRMED) {
            return response()->json(['status' => 'success']);
        }

        // Menggunakan Method Model untuk Cek Expired
        if ($pemesanan->checkAndCancelIfExpired()) {
            return response()->json(['status' => 'expired']);
        }

        if ($pemesanan->status_pemesanan === StatusPemesanan::CANCELLED) {
            return response()->json(['status' => 'expired']);
        }

        return response()->json(['status' => 'pending']);
    }

    public function cancelBooking($id): RedirectResponse
    {
        $pemesanan = Pemesanan::findOrFail($id);

        if ($pemesanan->user_id == Auth::id() && $pemesanan->status_pemesanan === StatusPemesanan::PENDING) {
            $pemesanan->update(['status_pemesanan' => StatusPemesanan::CANCELLED]);
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

        if ($pemesanan->status_pemesanan === StatusPemesanan::PENDING) {
            $pemesanan->update(['status_pemesanan' => StatusPemesanan::CONFIRMED]);
            return redirect()->route('dashboard')->with('success', 'Pembayaran Berhasil! Kamar Berhasil Dipesan.');
        }

        // Return Redirect Flash Message, bukan string
        return redirect()->route('dashboard')
            ->with('error', 'Pesanan tidak valid atau sudah diproses sebelumnya.');
    }
}
