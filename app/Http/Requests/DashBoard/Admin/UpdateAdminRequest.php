<?php

namespace App\Http\Requests\DashBoard\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
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
        $adminId = auth()->guard('admin')->id();

        return [
            'name'  => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                Rule::unique('admins', 'email')->ignore($adminId),
            ],

            'phone' => [
                'required',
                Rule::unique('admins', 'phone')->ignore($adminId),
            ],

            'old_password' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'confirmed', 'min:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required_with' => 'Old password is required',
            'password.confirmed' => 'Password confirmation does not match',
        ];
    }
}
