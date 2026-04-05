<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'openai_key' => ['nullable', 'string', 'max:255'],
        ], [
            'openai_key.max' => 'A chave é muito longa.',
        ]);

        auth()->user()->update([
            'openai_key' => $request->filled('openai_key')
                ? $request->openai_key
                : null,
        ]);

        return back()->with('success', 'Configurações salvas com sucesso!');
    }
}
