<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scholarship_id',
        'status',
    ];

    // Relationship: application belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: application belongs to a scholarship
    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }

    // Relationship: application has many requirements
    public function requirements()
    {
        return $this->hasMany(ApplicationRequirement::class);
    }
}
