<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $office
 */
class  OfficeImageRequest extends FormRequest
{
    #[ArrayShape(['image' => "string[]"])]
    public function rules(): array
    {
        return [
            'image' => ['file', 'max:5000', 'mimes:jpg,png']
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->can('update', $this->office);
    }
}
