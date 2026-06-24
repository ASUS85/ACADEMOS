<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('jury_user') && DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE jury_user MODIFY role ENUM('president', 'encadreur', 'rapporteur', 'membre') NOT NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('jury_user') && DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE jury_user MODIFY role ENUM('president', 'encadreur', 'membre') NOT NULL");
        }
    }
};
