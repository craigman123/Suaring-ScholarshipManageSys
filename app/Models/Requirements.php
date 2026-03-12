<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirements extends Model
{
    use HasFactory;

    protected $fillable = ['scholarship_id', 'requirements'];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }
}
