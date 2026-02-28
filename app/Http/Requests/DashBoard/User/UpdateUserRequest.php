<?php

namespace App\Http\Requests\DashBoard\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user),
            ],
            'phone' => [
                'sometimes',
                'string',
                'max:13',
                Rule::unique('users', 'phone')->ignore($this->user),
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'city'        => 'sometimes|string|max:255',
            'region'      => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required_with' => 'Old password is required',
            'password.confirmed' => 'Password confirmation does not match',
            'category_id.required' => 'Category is required',
        ];
    }
}
