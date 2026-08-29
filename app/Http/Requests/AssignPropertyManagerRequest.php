<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignPropertyManagerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isOwner() || auth()->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'manager_email' => ['required', 'email', 'exists:users,email'],
            'property_id' => ['nullable', 'integer'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ];
    }
}
