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

    protected $casts = [
        'requirements' => 'array',
    ];

    public function requirement()
    {
        return $this->hasOne(Requirements::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
