<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'image_path', 'title', 'description', 'deadline', 'status', 'requirements', 'provider_id',
    ];

    public function requirement()
    {
        return $this->hasOne(Requirements::class, 'scholarship_id');
    }

    public function requirementsForAPI()
    {
        return $this->hasMany(Requirements::class, 'scholarship_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
