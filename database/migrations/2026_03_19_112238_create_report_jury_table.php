<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('report_jury', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_president')->default(false);
            $table->timestamps();

            // Index unique (1 seul membre par rapport+user)
            $table->unique(['report_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_jury');
    }
};
