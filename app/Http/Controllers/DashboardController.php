<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Notification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Make sure this query is returning data
        $notifications = Notification::with(['post.user'])
            ->where('user_id', auth()->id()) // ONLY user related notifications
            ->where(function($query) {
                $query->where('is_read', false)
                    ->orWhere('created_at', '>=', now()->subDays(7));
            })
            ->latest()
            ->get();

        return Inertia::render('Dashboard', [
            'categories' => Category::all(['category_id', 'category_name']),
            'notifications' => $notifications,
        ]);
    }
}
