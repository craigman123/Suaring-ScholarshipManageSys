<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'bio',
        'image',
        'course',
        'institution',
        'address',
        'city',
        'state',
        'country',
        'zip',
        'gender',
        'dob',
        'marital_status',
        'religion',
        'nationality',
        'achievements',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
