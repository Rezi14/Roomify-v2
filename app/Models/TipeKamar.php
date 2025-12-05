<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipeKamar extends Model
{
    use HasFactory;

    protected $table = 'tipe_kamars';
    protected $primaryKey = 'id_tipe_kamar';

    protected $fillable = [
        'nama_tipe_kamar',
        'harga_per_malam',
        'kapasitas',
        'deskripsi',
        'foto_url',
    ];

    protected $casts = [
        'harga_per_malam' => 'float',
        'kapasitas' => 'integer',
    ];

    // Relasi ke Kamar (One-to-Many).
    // Satu tipe kamar bisa memiliki banyak kamar fisik.
    public function kamars(): HasMany
    {
        return $this->hasMany(Kamar::class, 'id_tipe_kamar', 'id_tipe_kamar');
    }

    // Relasi ke Fasilitas (Many-to-Many).
    // Fasilitas default yang termasuk dalam tipe kamar ini (misal: AC, WiFi).
    public function fasilitas(): BelongsToMany
    {
        return $this->belongsToMany(Fasilitas::class, 'tipe_kamar_fasilitas', 'id_tipe_kamar', 'id_fasilitas')
            ->withTimestamps();
    }
}
