<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserLogin extends FormRequest
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
            'email'=>'required|email|exists:users,email',
            'password'=>'required|string',
        ];
    }

    public function messages():array{
        return[
        'email.required'=>'The email field is required',
        'email.email'=>'The email must be a valid email address.',
        'email.exists'=>'The email has not been registered.',
        'password.required'=>'The password field is required.',
        'password.string'=>'The password must be a string.',
        ];
    }
}
