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
            'locale'     => ['nullable', 'string', 'in:pt,en,es'],
        ], [
            'openai_key.max' => __('app.messages.key_too_long'),
        ]);

        $updates = [];

        if ($request->filled('locale')) {
            $updates['locale'] = $request->locale;
        }

        if ($request->filled('openai_key')) {
            $updates['openai_key'] = $request->openai_key;
        } elseif ($request->boolean('remove_key')) {
            $updates['openai_key'] = null;
        }

        auth()->user()->update($updates);

        return back()->with('success', __('app.settings.saved'));
    }
}
