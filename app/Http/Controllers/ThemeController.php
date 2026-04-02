<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function toggle(Request $request)
    {
        $user = auth()->user();
        $user->theme = $user->theme === 'dark' ? 'light' : 'dark';
        $user->save();

        return back();
    }
}
