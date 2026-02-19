<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('version')->default('v1');
            $table->string('file_path');
            $table->text('comment')->nullable();
            $table->enum('action', ['soumis', 'modifié', 'commenté', 'validé'])->default('soumis');
            $table->timestamps();

            $table->index(['report_id', 'version']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_versions');
    }
};
