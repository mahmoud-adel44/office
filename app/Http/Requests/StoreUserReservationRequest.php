<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserReservationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => ['required', 'date:Y-m-d', 'after:today'],
            'end_date' => ['required', 'date:Y-m-d', 'after:start_date'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
