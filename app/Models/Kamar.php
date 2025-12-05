<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    use HasFactory;

    protected $table = 'kamars';
    protected $primaryKey = 'id_kamar';

    protected $fillable = [
        'nomor_kamar',
        'id_tipe_kamar',
        'status_kamar',
    ];

    protected $casts = [
        'status_kamar' => 'boolean',
    ];

    // Relasi ke Tipe Kamar.
    public function tipeKamar(): BelongsTo
    {
        return $this->belongsTo(TipeKamar::class, 'id_tipe_kamar', 'id_tipe_kamar');
    }

    // Relasi ke Pemesanan.
    public function pemesanans(): HasMany
    {
        return $this->hasMany(Pemesanan::class, 'kamar_id', 'id_kamar');
    }
}
