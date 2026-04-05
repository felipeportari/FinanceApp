<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'        => ['required', 'in:expense,income'],
            'amount'      => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'category_id' => [
                'required',
                // Must be a category visible to this user (default or own)
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where(function ($q) {
                        $q->whereNull('user_id')
                          ->orWhere('user_id', auth()->id());
                    });
                }),
            ],
            'description' => ['required', 'string', 'min:3', 'max:255'],
            'date'        => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'        => 'O tipo é obrigatório.',
            'type.in'              => 'O tipo deve ser gasto ou lucro.',
            'amount.required'      => 'O valor é obrigatório.',
            'amount.numeric'       => 'O valor deve ser numérico.',
            'amount.min'           => 'O valor mínimo é R$ 0,01.',
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists'   => 'Categoria inválida.',
            'description.required' => 'A descrição é obrigatória.',
            'description.min'      => 'A descrição deve ter pelo menos 3 caracteres.',
            'date.required'        => 'A data é obrigatória.',
            'date.before_or_equal' => 'A data não pode ser futura.',
        ];
    }
}
