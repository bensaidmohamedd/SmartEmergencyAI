<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE signalements MODIFY statut ENUM('en_cours', 'termine', 'annule') NOT NULL DEFAULT 'en_cours'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE signalements MODIFY statut ENUM('en_cours', 'termine') NOT NULL DEFAULT 'en_cours'");
        }
    }
};
