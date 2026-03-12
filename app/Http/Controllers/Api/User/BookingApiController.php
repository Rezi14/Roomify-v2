<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\StatusPemesanan;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Pemesanan;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingApiController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    /**
     * Memetakan BookingController@showBookingForm
     * Mengembalikan data form booking (detail kamar, fasilitas tersedia, max tamu).
     */
    public function showBookingForm(Kamar $kamar): JsonResponse
    {
        // Cek pending booking (sama dengan BookingController)
        $pendingBooking = Pemesanan::where('user_id', Auth::id())
            ->where('status_pemesanan', StatusPemesanan::PENDING)
            ->first();

        if ($pendingBooking) {
            return response()->json([
                'message' => 'Anda masih memiliki pesanan yang belum diselesaikan.',
                'pending_pemesanan_id' => $pendingBooking->id_pemesanan,
            ], 409);
        }

        $kamar->load('tipeKamar.fasilitas');
        $fasilitasTersedia = Fasilitas::where('biaya_tambahan', '>', 0)->get();

        return response()->json([
            'kamar' => $kamar,
            'max_tamu' => $kamar->tipeKamar->kapasitas,
            'fasilitas_tersedia' => $fasilitasTersedia,
        ]);
    }

    /**
     * Memetakan BookingController@store
     * Menggunakan StoreBookingRequest yang sudah ada & BookingService.
     */
    public function store(StoreBookingRequest $request): JsonResponse
    {
        // Cek pending booking
        $pendingBooking = Pemesanan::where('user_id', Auth::id())
            ->where('status_pemesanan', StatusPemesanan::PENDING)
            ->first();

        if ($pendingBooking) {
            return response()->json([
                'message' => 'Selesaikan pembayaran transaksi sebelumnya.',
                'pending_pemesanan_id' => $pendingBooking->id_pemesanan,
            ], 409);
        }

        $checkIn  = $request->check_in_date;
        $checkOut = $request->check_out_date;

        try {
            return DB::transaction(function () use ($request, $checkIn, $checkOut): JsonResponse {
                if (!$this->bookingService->isRoomAvailable((int) $request->kamar_id, $checkIn, $checkOut)) {
                    return response()->json([
                        'message' => 'Maaf, kamar tidak tersedia pada tanggal yang dipilih.',
                    ], 422);
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

                // Attach fasilitas dengan data pivot
                if (!empty($fasilitasData)) {
                    $pemesanan->fasilitas()->attach($fasilitasData);
                }

                $pemesanan->load(['kamar.tipeKamar', 'fasilitas', 'user']);

                return response()->json([
                    'message' => 'Pesanan berhasil dibuat! Silakan bayar.',
                    'pemesanan' => $pemesanan,
                ], 201);
            });

        } catch (\Exception $e) {
            Log::error('API Booking error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
            ], 500);
        }
    }

    /**
     * Memetakan BookingController@showPayment
     * Mengembalikan data pembayaran + sisa waktu.
     */
    public function showPayment(int $id): JsonResponse
    {
        $pemesanan = Pemesanan::with(['kamar.tipeKamar', 'fasilitas'])->findOrFail($id);

        if ($pemesanan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($pemesanan->status_pemesanan !== StatusPemesanan::PENDING) {
            return response()->json([
                'message' => 'Pemesanan sudah diproses.',
                'status' => $pemesanan->status_pemesanan,
            ]);
        }

        // Cek expired (sama dengan BookingController)
        if ($pemesanan->checkAndCancelIfExpired()) {
            return response()->json([
                'message' => 'Waktu pembayaran telah habis. Pesanan dibatalkan.',
                'status' => 'cancelled',
            ], 410);
        }

        // Hitung sisa waktu
        $waktuDibuat = Carbon::parse($pemesanan->created_at);
        $batasWaktu  = $waktuDibuat->copy()->addMinutes(10);
        $sisaDetik   = Carbon::now()->diffInSeconds($batasWaktu, false);

        return response()->json([
            'pemesanan' => $pemesanan,
            'batas_waktu' => $batasWaktu->toIso8601String(),
            'sisa_detik' => max($sisaDetik, 0),
        ]);
    }

    /**
     * Memetakan BookingController@checkPaymentStatus
     * Polling status pembayaran.
     */
    public function checkPaymentStatus(int $id): JsonResponse
    {
        $pemesanan = Pemesanan::findOrFail($id);

        if ($pemesanan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($pemesanan->status_pemesanan === StatusPemesanan::CONFIRMED) {
            return response()->json(['status' => 'success']);
        }

        if ($pemesanan->checkAndCancelIfExpired()) {
            return response()->json(['status' => 'expired']);
        }

        if ($pemesanan->status_pemesanan === StatusPemesanan::CANCELLED) {
            return response()->json(['status' => 'expired']);
        }

        return response()->json(['status' => 'pending']);
    }

    /**
     * Memetakan BookingController@cancelBooking
     */
    public function cancelBooking(int $id): JsonResponse
    {
        $pemesanan = Pemesanan::findOrFail($id);

        if ($pemesanan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        if ($pemesanan->status_pemesanan !== StatusPemesanan::PENDING) {
            return response()->json([
                'message' => 'Hanya pesanan dengan status pending yang bisa dibatalkan.',
            ], 422);
        }

        $pemesanan->update(['status_pemesanan' => StatusPemesanan::CANCELLED]);

        return response()->json([
            'message' => 'Pesanan berhasil dibatalkan.',
        ]);
    }

    /**
     * Memetakan BookingController@detail
     */
    public function detail(int $id): JsonResponse
    {
        $pemesanan = Pemesanan::with(['kamar.tipeKamar.fasilitas', 'user', 'fasilitas'])->findOrFail($id);

        if (Auth::id() !== $pemesanan->user_id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json([
            'pemesanan' => $pemesanan,
        ]);
    }
}
