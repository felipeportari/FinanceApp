<?php

namespace App\Http\Controllers;

use App\Services\FinancialService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $service = new FinancialService(auth()->id());

        $monthlyEvolution = $service->monthlyEvolution(6);
        $expensesByCategory = $service->expensesByCategory();
        $topCategory = $service->topExpenseCategory();

        return view('dashboard.index', [
            'totalExpenses' => $service->currentMonthExpenses(),
            'totalIncomes' => $service->currentMonthIncomes(),
            'monthBalance' => $service->currentMonthBalance(),
            'totalBalance' => $service->totalBalance(),
            'monthlyEvolution' => $monthlyEvolution,
            'expensesByCategory' => $expensesByCategory,
            'topCategory' => $topCategory,
        ]);
    }
}
