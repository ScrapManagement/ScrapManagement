<?php

namespace App\Http\Requests\DashBoard\Package;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePackageRequest extends FormRequest
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
        $packageId = $this->route('id');
        return [
            'name'      => 'sometimes|string|max:255|unique:packages,name,' . $packageId,
            'price'     => 'sometimes|numeric|min:0',
            'coins'     => 'sometimes|integer|min:1',
            'is_active' => 'sometimes|boolean',
        ];
    }
}
