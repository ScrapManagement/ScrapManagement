<?php

namespace App\Http\Requests\DashBoard\User;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name'     => 'required|string|max:255',
            'email'       => 'nullable|email|max:255|unique:users,email',
            'phone'       => 'required|string|max:12|unique:users,phone',
            'password'    => 'required|string|min:6|confirmed',
            'city'        => 'required|string|max:255',
            'region'      => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ];
    }

     public function messages(): array
    {
        return [
            'password.confirmed' => 'Password confirmation does not match',
            'category_id.required' => 'Category is required',
        ];
    }
}
