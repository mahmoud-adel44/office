<?php

namespace App\Http\Requests;

use App\Models\Office;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property Office $office
 */
class StoreOfficeRequest extends FormRequest
{
    public function rules(): array
    {

        return [
            'title' => [Rule::when($this->office?->exists, 'sometimes'), 'required', 'string'],
            'description' => [Rule::when($this->office?->exists, 'sometimes'), 'required', 'string'],
            'lat' => [Rule::when($this->office?->exists, 'sometimes'), 'required', 'numeric'],
            'lng' => [Rule::when($this->office?->exists, 'sometimes'), 'required', 'numeric'],
            'address_line1' => [Rule::when($this->office?->exists, 'sometimes'), 'required', 'string'],
            'address_line2' => ['string'],
            'price_per_day' => [Rule::when($this->office?->exists, 'sometimes'), 'required', 'integer', 'min:100'],


            'featured_image_id' => [Rule::exists('images', 'id')->where('resource_type', 'office')->where('resource_id', $this->office?->id)],
            'hidden' => ['bool'],
            'monthly_discount' => ['integer', 'min:0', 'max:90'],

            'tags' => ['array'],
            'tags.*' => ['integer', Rule::exists('tags', 'id')]
        ];
    }

    public function authorize(): bool
    {
        if ($this->office) {
            return $this->user()->can('update', $this->office);
        }
        return true;

    }


}
