<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'title',
        'category_id',
        'content',
        'image_path',
        'approval_status'
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function scopeFilter(Builder $query, $request)
    {
        return $query
            ->when($request->search, function ($q) use ($request) {
                $q->where(function($query) use ($request) {
                    $query->where('title', 'like', "%{$request->search}%")
                          ->orWhere('content', 'like', "%{$request->search}%");
                });
            })
            ->when($request->category, function ($q) use ($request) {
                $q->where('category_id', (int)$request->category);
            })
            ->when($request->author, function ($q) use ($request) {
                $q->where('user_id', (int)$request->author);
            })
            ->when($request->sort, function ($q) use ($request) {
                switch ($request->sort) {
                    case 'oldest':
                        $q->oldest();
                        break;
                    case 'title_asc':
                        $q->orderBy('title', 'asc');
                        break;
                    case 'title_desc':
                        $q->orderBy('title', 'desc');
                        break;
                    default:
                        $q->latest();
                }
            });
    }
}
