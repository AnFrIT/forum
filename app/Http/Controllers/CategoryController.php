<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        if (! $category->is_active) {
            abort(404);
        }

        $topics = $category->topics()
            ->with(['user', 'lastPostUser'])
            ->visible()
            ->ordered()
            ->paginate(setting('topics_per_page', 25));

        $subcategories = $category->children()
            ->active()
            ->orderBy('order')
            ->get();

        return view('categories.show', compact('category', 'topics', 'subcategories'));
    }
}
