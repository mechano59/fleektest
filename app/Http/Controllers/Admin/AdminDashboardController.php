<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $pendingPosts = Post::where('approval_status', 'pending')->with(['user', 'category'])->get();
        
        return Inertia::render('Admin/Dashboard', [
            'pendingPosts' => $pendingPosts,
        ]);
    }
}