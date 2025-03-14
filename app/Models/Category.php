<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id'; // Custom primary key

    protected $fillable = ['category_name'];

    // Relationship with posts
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
