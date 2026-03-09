<?php

namespace App\Models;

use App\Enums\StatusPemesanan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemesanan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pemesanans';
    protected $primaryKey = 'id_pemesanan';

    protected $fillable = [
        'user_id',
        'kamar_id',
        'check_in_date',
        'check_out_date',
        'jumlah_tamu',
        'total_harga',
        'status_pemesanan',
    ];

    protected $casts = [
        'check_in_date'    => 'date',
        'check_out_date'   => 'date',
        'total_harga'      => 'decimal:2',
        'status_pemesanan' => StatusPemesanan::class,
    ];

    // Relasi user(), kamar(), fasilitas()

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(Kamar::class, 'kamar_id', 'id_kamar');
    }

    public function fasilitas(): BelongsToMany
    {
        return $this->belongsToMany(Fasilitas::class, 'pemesanan_fasilitas', 'id_pemesanan', 'id_fasilitas')
            ->withPivot('jumlah', 'total_harga_fasilitas')
            ->withTimestamps();
    }

    public function scopeOverlapping($query, $kamarId, $checkIn, $checkOut, $excludeId = null)
    {
        return $query->where('kamar_id', $kamarId)
            ->where('status_pemesanan', '!=', StatusPemesanan::CANCELLED)
            ->where('status_pemesanan', '!=', StatusPemesanan::CHECKED_OUT)
            ->when($excludeId, fn($q) => $q->where('id_pemesanan', '!=', $excludeId))
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);
            });
    }

    // Mengecek apakah pesanan kadaluarsa (lebih dari 10 menit).
    // Mengembalikan true jika pesanan dibatalkan karena expired.
    public function checkAndCancelIfExpired(): bool
    {
        if ($this->status_pemesanan !== StatusPemesanan::PENDING) {
            return false;
        }

        // membatasi waktu 10 menit dari created_at
        $batasWaktu = $this->created_at->copy()->addMinutes(10);

        if (Carbon::now()->greaterThan($batasWaktu)) {
            $this->update(['status_pemesanan' => StatusPemesanan::CANCELLED]);
            return true;
        }

        return false;
    }
}
