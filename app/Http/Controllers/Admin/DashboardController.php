<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'total_transactions' => Transaction::count(),
            'total_expenses' => Transaction::where('type', 'expense')->sum('amount'),
            'total_income' => Transaction::where('type', 'income')->sum('amount'),
        ];

        $recentUsers = User::latest()->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }
}
