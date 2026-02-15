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
        Schema::table('reports', function (Blueprint $table) {
            $table->integer('jury_technical_note')->nullable()->after('teacher_status');
            $table->integer('jury_presentation_note')->nullable()->after('jury_technical_note');
            $table->integer('jury_content_note')->nullable()->after('jury_presentation_note');
            $table->decimal('jury_final_score', 4, 2)->nullable()->after('jury_content_note');
            $table->enum('jury_decision', ['Validé', 'Rejeté', 'À revoir'])->nullable()->after('jury_final_score');
            $table->text('jury_comment')->nullable()->after('jury_decision');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            //
        });
    }
};
