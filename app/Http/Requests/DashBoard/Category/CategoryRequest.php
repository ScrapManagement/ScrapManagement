<?php

namespace App\Http\Requests\DashBoard\Category;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:categories,name,' . $categoryId,
            'material_priority' => 'required_without:parent_id|nullable|integer|min:1|max:5',
            'parent_id' => 'sometimes|nullable|exists:categories,id|not_in:' . $categoryId,
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Category name is required',
            'name.unique'        => 'This category name already exists',
            'name.string'        => 'Name must be a string',
            'name.max'           => 'Name must not exceed 255 characters',
            'material_priority.required_without' => 'Material priority is required for main categories',
            'material_priority.integer' => 'Material priority must be an integer',
            'material_priority.min' => 'Material priority must be at least 1',
            'material_priority.max' => 'Material priority must not exceed 5',
            'parent_id.exists'   => 'Parent category not found',
            'parent_id.not_in'   => 'Category cannot be its own parent',
        ];
    }
}
