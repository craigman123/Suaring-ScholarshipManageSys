<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{

    use HasFactory;

    protected $fillable = [
        'image_path', 'title', 'description', 'deadline', 'status'
    ];

    public function requirement()
    {
        return $this->hasOne(\App\Models\Requirements::class);
    }
}
