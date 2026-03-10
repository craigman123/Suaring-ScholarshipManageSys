<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    protected $fillable = [
        'scholarship_id', 'requirement'
    ];

    public function scholarship()
    {
        return $this->belongsTo(\App\Models\Scholarship::class);
    }
}
