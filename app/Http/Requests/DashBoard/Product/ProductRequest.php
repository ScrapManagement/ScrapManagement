<?php

namespace App\Http\Requests\DashBoard\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:1',
            'unit'        => 'required|string|max:50',
            'category_id' => 'required|exists:categories,id',
            'sale_type' => 'nullable|in:coins,auction',
            'images'      => 'required|array|min:1',
            'images.*'    => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'At least one image is required',
            'images.*.image'  => 'Each file must be an image',
            'category_id.required' => 'Category is required',
        ];
    }
}
