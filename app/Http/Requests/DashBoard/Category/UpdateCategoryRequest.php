<?php

namespace App\Http\Requests\DashBoard\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
        $categoryId = $this->route('id');

        return [
            'name'      => 'sometimes|string|max:255|unique:categories,name,' . $categoryId,
            'parent_id' => 'sometimes|nullable|exists:categories,id|not_in:' . $categoryId,
        ];
    }

     public function messages(): array
    {
        return [
            'name.unique'        => 'This category name already exists',
            'name.string'        => 'Name must be a string',
            'name.max'           => 'Name must not exceed 255 characters',
            'parent_id.exists'   => 'Parent category not found',
            'parent_id.not_in'   => 'Category cannot be its own parent',
        ];
    }
}
