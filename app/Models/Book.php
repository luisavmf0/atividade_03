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

    protected $fillable = ['title', 'isbn', 'author_id', 'category_id', 'publisher_id', 'pages'];

    // CORREÇÃO: Um livro PERTENCE A um autor (no singular)
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    // Aproveite para garantir que os outros também usem belongsTo:
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
}