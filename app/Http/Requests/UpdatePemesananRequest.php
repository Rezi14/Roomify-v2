<?php

namespace App\Http\Requests;

use App\Enums\StatusPemesanan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePemesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled by admin middleware at the route level
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id'            => ['required', 'exists:users,id'],
            'kamar_id'           => ['required', 'exists:kamars,id_kamar'],
            'check_in_date'      => ['required', 'date'],
            'check_out_date'     => ['required', 'date', 'after:check_in_date'],
            'jumlah_tamu'        => ['required', 'integer', 'min:1'],
            'status_pemesanan'   => ['required', new Enum(StatusPemesanan::class)],
            'fasilitas_tambahan' => ['nullable', 'array'],
            'fasilitas_tambahan.*' => ['exists:fasilitas,id_fasilitas'],
            'total_harga'        => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
