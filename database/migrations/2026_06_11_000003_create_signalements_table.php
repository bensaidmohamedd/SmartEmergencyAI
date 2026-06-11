<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 20)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->text('description');
            $table->string('localisation');
            $table->enum('gravite', ['critique', 'elevee', 'moyenne', 'faible']);
            $table->enum('statut', ['en_cours', 'termine'])->default('en_cours');
            $table->string('photo')->nullable();
            $table->timestamp('reported_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};
