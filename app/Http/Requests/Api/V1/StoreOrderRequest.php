<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'event_id.required' => "L'événement est obligatoire.",
            'event_id.integer' => "L'identifiant de l'événement doit être un entier.",
            'event_id.exists' => "L'événement sélectionné n'existe pas.",
            'quantity.required' => "La quantité est obligatoire.",
            'quantity.integer' => "La quantité doit être un nombre entier.",
            'quantity.min' => "La quantité doit être d'au moins 1.",
        ];
    }
}


