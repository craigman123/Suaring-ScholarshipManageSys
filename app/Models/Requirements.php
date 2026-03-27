<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirements extends Model
{
    use HasFactory;

    protected $fillable = ['scholarship_id', 'requirements'];

    protected $casts = [
        'requirements' => 'array', 
    ];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }
}
