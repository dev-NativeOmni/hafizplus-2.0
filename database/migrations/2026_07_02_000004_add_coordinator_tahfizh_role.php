<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Role::updateOrCreate(
            ['name' => 'coordinator_tahfizh'],
            [
                'name' => 'coordinator_tahfizh',
                'display_name' => 'Koordinator Tahfizh',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Role::where('name', 'coordinator_tahfizh')->delete();
    }
};
