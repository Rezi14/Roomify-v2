<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization is handled by middleware (auth, verified)
        return true;
    }

    public function rules(): array
    {
        return [
            'kamar_id'       => ['required', 'exists:kamars,id_kamar'],
            'check_in_date'  => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
            'jumlah_tamu'    => ['required', 'integer', 'min:1'],
            'fasilitas_ids'  => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'check_in_date.after_or_equal' => 'Tanggal check-in tidak boleh di masa lalu.',
            'check_out_date.after'          => 'Tanggal check-out harus setelah tanggal check-in.',
        ];
    }
}
