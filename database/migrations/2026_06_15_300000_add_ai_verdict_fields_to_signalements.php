<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            $table->unsignedTinyInteger('ai_credibility_score')->nullable()->after('ai_score');
            $table->string('ai_verdict', 20)->default('approved')->after('ai_credibility_score');
            $table->json('ai_rejection_reasons')->nullable()->after('ai_verdict');
            $table->unsignedTinyInteger('ai_priority_rank')->nullable()->after('ai_rejection_reasons');
        });
    }

    public function down(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            $table->dropColumn([
                'ai_credibility_score', 'ai_verdict', 'ai_rejection_reasons', 'ai_priority_rank',
            ]);
        });
    }
};
