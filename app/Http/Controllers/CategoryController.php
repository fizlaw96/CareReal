<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('treatments')->orderBy('name')->get();

        return view('categories.index', [
            'title' => 'Pilih Kategori Rawatan',
            'categories' => $categories,
        ]);
    }
}
