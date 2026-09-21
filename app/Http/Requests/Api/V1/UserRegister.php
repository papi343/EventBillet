<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserRegister extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|string|max:20',
            'avatar' => 'required|image|max:2048',
            'role' => 'required|in:Admin,Participant,Organiser',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 6 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'phone.required' => 'The phone field is required.',
            'phone.max' => 'The phone may not be greater than 20 characters.',
            'avatar.required' => 'The avatar field is required.',
            'avatar.image' => 'The avatar must be an image.',
            'avatar.max' => 'The avatar may not be greater than 2048 kilobytes.',
            'role.required' => 'The role field is required.',
            'role.in' => 'The role must be one of the following: Admin, Participant, Organiser.',
        ];
    }
}
