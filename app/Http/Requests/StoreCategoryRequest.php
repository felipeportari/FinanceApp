<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'min:2', 'max:100',
                // Name must be unique within categories visible to this user
                Rule::unique('categories', 'name')->where(function ($query) {
                    $query->where(function ($q) {
                        $q->whereNull('user_id')
                          ->orWhere('user_id', auth()->id());
                    });
                }),
            ],
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon'  => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'O nome da categoria é obrigatório.',
            'name.unique'    => 'Já existe uma categoria com esse nome.',
            'color.required' => 'A cor é obrigatória.',
            'color.regex'    => 'Cor inválida. Use formato hexadecimal (#RRGGBB).',
            'icon.required'  => 'O ícone é obrigatório.',
        ];
    }
}
