<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_jury_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('technical_note', 4, 2);
            $table->decimal('presentation_note', 4, 2);
            $table->decimal('content_note', 4, 2);
            $table->decimal('final_score', 4, 2);
            $table->enum('decision', ['Validé', 'Rejeté', 'À revoir']);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['report_id', 'user_id']);
            $table->index(['report_id', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_jury_evaluations');
    }
};
