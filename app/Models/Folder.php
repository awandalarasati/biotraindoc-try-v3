<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'tna_code', 'folder_size'];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
