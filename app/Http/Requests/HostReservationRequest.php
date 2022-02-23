<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HostReservationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => [Rule::in([Reservation::STATUS_ACTIVE, Reservation::STATUS_CANCELLED])],
            'office_id' => ['integer'],
            'user_id' => ['integer'],
            'from_date' => ['date', 'required_with:to_date'],
            'to_date' => ['date', 'required_with:from_date', 'after:from_date'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
