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
            ->with('success', __('app.messages.category_created'));
    }

    public function destroy(Category $category)
    {
        if ($category->is_default) {
            return back()->with('error', __('app.messages.category_default_error'));
        }

        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        if ($category->transactions()->exists()) {
            return back()->with('error', __('app.messages.category_has_transactions'));
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', __('app.messages.category_deleted'));
    }
}
