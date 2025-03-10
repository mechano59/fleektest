<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostFilterRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(PostFilterRequest $request)
    {
        return Inertia::render('Welcome', [
            'posts' => Post::with(['user', 'category'])
                ->where('approval_status', 'approved')
                ->filter($request)
                ->paginate(10)
                ->withQueryString(),
            'categories' => Category::select('category_id as id', 'category_name as name')->get(),
            'authors' => User::has('posts')->select('id', 'name')->get(),
            'filters' => $request->validated(),
        ]);
    }

}
