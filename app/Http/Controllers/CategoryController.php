<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('transactions')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        Category::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function destroy(Category $category)
    {
        if ($category->is_default) {
            return back()->with('error', 'Categorias padrão não podem ser removidas.');
        }

        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        if ($category->transactions()->exists()) {
            return back()->with('error', 'Não é possível remover uma categoria com transações vinculadas.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }
}
