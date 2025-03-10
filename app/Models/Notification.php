<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'type',
        'message',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}