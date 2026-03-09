<?php

namespace App\Services;

use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Pemesanan;
use Carbon\Carbon;

class BookingService
{
    public function isRoomAvailable(int $kamarId, string $checkIn, string $checkOut, ?int $excludeId = null): bool
    {
        return !Pemesanan::overlapping($kamarId, $checkIn, $checkOut, $excludeId)
            ->lockForUpdate()
            ->exists();
    }

    public function calculateTotalPrice(Kamar $kamar, string $checkIn, string $checkOut, array $fasilitasIds = []): float
    {
        $durasi = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
        $total = $kamar->tipeKamar->harga_per_malam * max($durasi, 1);

        if (!empty($fasilitasIds)) {
            $total += Fasilitas::whereIn('id_fasilitas', $fasilitasIds)->sum('biaya_tambahan');
        }

        return $total;
    }

    public function prepareFasilitasPivotData(array $fasilitasIds): array
    {
        $pivotData = [];
        if (!empty($fasilitasIds)) {
            $fasilitasObjs = Fasilitas::whereIn('id_fasilitas', $fasilitasIds)->get();
            foreach ($fasilitasObjs as $f) {
                $pivotData[$f->id_fasilitas] = [
                    'jumlah' => 1,
                    'total_harga_fasilitas' => $f->biaya_tambahan,
                ];
            }
        }
        return $pivotData;
    }
}
