<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $featured   = Product::where('is_featured', true)->where('is_available', true)->with('category')->take(6)->get();
        $categories = Category::withCount('products')->get();

        return view('home', compact('featured', 'categories'));
    }
}