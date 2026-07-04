<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_points', function (Blueprint $table) {
            $table->string('category')->nullable()->after('points'); // 'ringan', 'sedang', 'berat' atau custom
            $table->text('sanction')->nullable()->after('description'); // sanksi yang diberikan
            $table->string('achievement_type')->nullable()->after('sanction'); // 'academic', 'non-academic'
            $table->string('achievement_level')->nullable()->after('achievement_type'); // 'school', 'district', 'province', 'national'
            $table->string('location')->nullable()->after('achievement_level'); // lokasi kejadian untuk heatmap
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_points', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'sanction',
                'achievement_type',
                'achievement_level',
                'location',
            ]);
        });
    }
};
