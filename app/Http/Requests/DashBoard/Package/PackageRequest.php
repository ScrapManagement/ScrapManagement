<?php

namespace App\Http\Requests\DashBoard\Package;

use Illuminate\Foundation\Http\FormRequest;

class PackageRequest extends FormRequest
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
            'name'      => 'required|string|max:255|unique:packages,name,' . $packageId,
            'price'     => 'required|numeric|min:0',
            'coins'     => 'required|integer|min:1',
            'is_active' => 'boolean',
        ];
    }
}
