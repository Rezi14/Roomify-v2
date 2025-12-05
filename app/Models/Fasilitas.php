<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';
    protected $primaryKey = 'id_fasilitas';

    protected $fillable = [
        'nama_fasilitas',
        'deskripsi',
        'icon',
        'biaya_tambahan',
    ];

    // Relasi ke Tipe Kamar (Fasilitas Dasar).
    public function tipeKamars(): BelongsToMany
    {
        return $this->belongsToMany(
            TipeKamar::class,
            'tipe_kamar_fasilitas',
            'id_fasilitas',
            'id_tipe_kamar'
        );
    }

    // Relasi ke Pemesanan (Fasilitas Tambahan).
    public function pemesanans(): BelongsToMany
    {
        return $this->belongsToMany(
            Pemesanan::class,
            'pemesanan_fasilitas',
            'id_fasilitas',
            'id_pemesanan'
        )->withTimestamps();
    }
}
