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
    Schema::table('users', function (Blueprint $table) {
        // On ajoute filiere_id (nullable car les admins/profs n'en ont pas)
        $table->foreignId('filiere_id')->nullable()->constrained('filieres')->onDelete('set null');
        // On ajoute aussi le matricule que ton seeder essaie d'utiliser
        $table->string('matricule')->nullable()->unique();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['filiere_id']);
        $table->dropColumn(['filiere_id', 'matricule']);
    });
}
};
