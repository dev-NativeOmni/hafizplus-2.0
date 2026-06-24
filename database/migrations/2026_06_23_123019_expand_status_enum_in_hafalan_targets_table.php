<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Memperluas enum status di tabel hafalan_targets.
     *
     * Menambahkan nilai 'planned' dan 'in_progress' yang sudah digunakan
     * di seluruh codebase (services, controllers, resources) namun belum
     * didefinisikan di skema database, menyebabkan nilai-nilai ini tidak
     * pernah cocok dengan baris manapun.
     *
     * Nilai lengkap setelah migrasi:
     * 'active', 'planned', 'in_progress', 'completed', 'missed', 'cancelled'
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // MySQL/MariaDB: ALTER TABLE untuk mengubah enum
            DB::statement("
                ALTER TABLE hafalan_targets
                MODIFY COLUMN status ENUM(
                    'active',
                    'planned',
                    'in_progress',
                    'completed',
                    'missed',
                    'cancelled'
                ) NOT NULL DEFAULT 'active'
            ");
        }

        // SQLite digunakan untuk testing: enum diimplementasikan via CHECK constraint.
        // SQLite tidak mendukung ALTER TABLE untuk mengubah CHECK constraint,
        // namun schema di testing otomatis dibuat ulang via RefreshDatabase
        // sehingga tidak perlu migration khusus untuk SQLite.
        // (Lihat: create_hafalan_targets_table.php — sudah diperbarui via seeder)
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement("
                ALTER TABLE hafalan_targets
                MODIFY COLUMN status ENUM(
                    'active',
                    'completed',
                    'missed',
                    'cancelled'
                ) NOT NULL DEFAULT 'active'
            ");
        }
    }
};
