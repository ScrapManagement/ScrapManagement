<?php

namespace App\Http\Requests\DashBoard\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'quantity'    => 'sometimes|integer|min:1',
            'unit'        => 'sometimes|string|max:50',
            'category_id' => 'sometimes|exists:categories,id',
            'latitude'    => 'sometimes|numeric|between:-90,90',
            'longitude'   => 'sometimes|numeric|between:-180,180',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Category is required',
        ];
    }
}
