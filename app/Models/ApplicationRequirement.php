<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'file_path',
        'passed',
    ];
    
    // Relationship: requirement belongs to an application
    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
