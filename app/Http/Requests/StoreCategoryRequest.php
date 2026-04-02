<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'min:2', 'max:100', 'unique:categories,name'],
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
