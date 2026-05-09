<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Author;
use App\Models\Category;
use App\Models\Publisher;

class Book extends Model
{
    use HasFactory;
    
    protected $fillable = ['title', 'pages', 'author_id', 'category_id', 'publisher_id', 'published_year'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'borrowings')
                    ->withPivot('borrowed_at', 'returned_at')
                    ->withTimestamps();
    }
}