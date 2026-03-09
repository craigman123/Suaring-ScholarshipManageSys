<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\YearLevel;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('year_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $yearLevels = [
            ['name' => '1st Year'],
            ['name' => '2nd Year'],
            ['name' => '3rd Year'],
            ['name' => '4th Year'],
        ];

        foreach($yearLevels as $yearLevel){
            YearLevel::create($yearLevel);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('year_levels');
    }
};
