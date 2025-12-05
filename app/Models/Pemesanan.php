<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Pemesanan extends Model
{
    use HasFactory;

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
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_harga' => 'decimal:2',
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

    // Mengecek apakah pesanan kadaluarsa (lebih dari 10 menit).
    // Mengembalikan true jika pesanan dibatalkan karena expired.
    public function checkAndCancelIfExpired(): bool
    {
        if ($this->status_pemesanan !== 'pending') {
            return false;
        }

        // membatasi waktu 10 menit dari created_at
        $batasWaktu = $this->created_at->addMinutes(10);

        if (Carbon::now()->greaterThan($batasWaktu)) {
            $this->update(['status_pemesanan' => 'cancelled']);
            return true;
        }

        return false;
    }
}
