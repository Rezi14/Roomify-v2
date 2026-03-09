<?php

namespace App\Http\Requests;

use App\Enums\StatusPemesanan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePemesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled by admin middleware at the route level
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'kamar_id'           => ['required', 'exists:kamars,id_kamar'],
            'check_in_date'      => ['required', 'date', 'after_or_equal:today'],
            'check_out_date'     => ['required', 'date', 'after:check_in_date'],
            'jumlah_tamu'        => ['required', 'integer', 'min:1'],
            'total_harga'        => ['required', 'numeric', 'min:0'],
            'status_pemesanan'   => ['required', new Enum(StatusPemesanan::class)],
            'fasilitas_tambahan' => ['nullable', 'array'],
            'fasilitas_tambahan.*' => ['exists:fasilitas,id_fasilitas'],
            'customer_type'      => ['required', 'string', 'in:existing,new'],
        ];

        if ($this->input('customer_type') === 'new') {
            $rules['new_user_name']  = ['required', 'string', 'max:255'];
            $rules['new_user_email'] = ['required', 'string', 'email', 'max:255', 'unique:users,email'];
        } else {
            $rules['user_id'] = ['required', 'exists:users,id'];
        }

        return $rules;
    }
}
