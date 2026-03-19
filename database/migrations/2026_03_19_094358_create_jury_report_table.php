<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jury_report', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_president')->default(false);
            $table->timestamps();

            $table->unique(['report_id', 'user_id']);
            $table->unique(['report_id', 'is_president'], 'report_president_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jury_report');
    }
};
