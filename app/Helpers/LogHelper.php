<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class LogHelper
{
    public static function log($action, $description = null, $user = null)
    {
        DB::table('logs')->insert([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}