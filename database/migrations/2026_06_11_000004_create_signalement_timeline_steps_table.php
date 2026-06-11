<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signalement_timeline_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signalement_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->boolean('done')->default(false);
            $table->timestamp('occurred_at')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signalement_timeline_steps');
    }
};
