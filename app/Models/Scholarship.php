<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = [
        'image_path', 'title', 'description', 'deadline', 'status'
    ];

    public function requirements()
    {
        return $this->hasMany(\App\Models\Requirement::class);
    }
}
