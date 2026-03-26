<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],

            // Foto de perfil
            'avatar'     => ['nullable', 'image', 'max:2048'],

            // Métricas físicas
            'birthdate'  => ['nullable', 'date', 'before:today'],
            'height_cm'  => ['nullable', 'integer', 'min:50', 'max:300'],
            'weight_kg'  => ['nullable', 'numeric', 'min:20', 'max:500'],
            'gender'     => ['nullable', Rule::in(['male', 'female', 'other'])],
            'locale'     => ['required', Rule::in(['pt', 'en', 'es'])],
            'is_private' => ['boolean'],
        ];
    }
}