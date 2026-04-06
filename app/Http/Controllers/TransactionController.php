<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Models\Category;
use App\Models\Transaction;
use App\Services\FinancialService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $service = new FinancialService(auth()->id());

        $filters = $request->only(['type', 'category_id', 'month', 'year', 'search']);
        $transactions = $service->paginatedTransactions($filters);
        $categories = Category::orderBy('name')->get();

        return view('transactions.index', compact('transactions', 'categories', 'filters'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('transactions.create', compact('categories'));
    }

    public function store(StoreTransactionRequest $request)
    {
        Transaction::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('transactions.index')
            ->with('success', __('app.messages.transaction_stored'));
    }

    public function edit(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $categories = Category::orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(StoreTransactionRequest $request, Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $transaction->update($request->validated());

        return redirect()->route('transactions.index')
            ->with('success', __('app.messages.transaction_updated'));
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeTransaction($transaction);

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', __('app.messages.transaction_deleted'));
    }

    private function authorizeTransaction(Transaction $transaction): void
    {
        abort_if($transaction->user_id !== auth()->id(), 403, __('app.messages.unauthorized'));
    }
}
