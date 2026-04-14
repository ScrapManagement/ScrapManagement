<?php

namespace App\Http\Requests\DashBoard\Auction;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAuctionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'starting_price'  => 'sometimes|numeric|min:1',
            'insurance_rate'  => 'sometimes|numeric|min:1|max:100',
            'starts_at'       => 'sometimes|date|after:now',
            'ends_at'         => 'sometimes|date|after:starts_at',
        ];
    }
}
