<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id_fasilitas
 * @property string $nama_fasilitas
 * @property string|null $deskripsi
 * @property string|null $icon
 * @property string $biaya_tambahan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pemesanan> $pemesanans
 * @property-read int|null $pemesanans_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TipeKamar> $tipeKamars
 * @property-read int|null $tipe_kamars_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fasilitas newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fasilitas newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fasilitas query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fasilitas whereBiayaTambahan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fasilitas whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fasilitas whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fasilitas whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fasilitas whereIdFasilitas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fasilitas whereNamaFasilitas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fasilitas whereUpdatedAt($value)
 */
	class Fasilitas extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_kamar
 * @property string $nomor_kamar
 * @property int $id_tipe_kamar
 * @property bool $status_kamar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pemesanan> $pemesanans
 * @property-read int|null $pemesanans_count
 * @property-read \App\Models\TipeKamar $tipeKamar
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereIdKamar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereIdTipeKamar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereNomorKamar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereStatusKamar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Kamar whereUpdatedAt($value)
 */
	class Kamar extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_pemesanan
 * @property int $user_id
 * @property int $kamar_id
 * @property \Illuminate\Support\Carbon $check_in_date
 * @property \Illuminate\Support\Carbon $check_out_date
 * @property int $jumlah_tamu
 * @property numeric $total_harga
 * @property string $status_pemesanan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Fasilitas> $fasilitas
 * @property-read int|null $fasilitas_count
 * @property-read \App\Models\Kamar $kamar
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan whereCheckInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan whereCheckOutDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan whereIdPemesanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan whereJumlahTamu($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan whereKamarId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan whereStatusPemesanan($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan whereTotalHarga($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pemesanan whereUserId($value)
 */
	class Pemesanan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_role
 * @property string $nama_role
 * @property string|null $deskripsi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereIdRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereNamaRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id_tipe_kamar
 * @property string $nama_tipe_kamar
 * @property float $harga_per_malam
 * @property string|null $deskripsi
 * @property string|null $foto_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $kapasitas
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Fasilitas> $fasilitas
 * @property-read int|null $fasilitas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Kamar> $kamars
 * @property-read int|null $kamars_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar whereDeskripsi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar whereFotoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar whereHargaPerMalam($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar whereIdTipeKamar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar whereKapasitas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar whereNamaTipeKamar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TipeKamar whereUpdatedAt($value)
 */
	class TipeKamar extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $id_role
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Pemesanan> $pemesanans
 * @property-read int|null $pemesanans_count
 * @property-read \App\Models\Role|null $role
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

