<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // pompiers, police, samu, gendarmerie
            $table->string('phone', 30);
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('zone')->nullable();
            $table->timestamps();
        });

        Schema::table('signalements', function (Blueprint $table) {
            $table->unsignedTinyInteger('ai_score')->nullable()->after('gravite');
            $table->text('ai_summary')->nullable()->after('ai_score');
            $table->json('ai_services')->nullable()->after('ai_summary');
            $table->unsignedSmallInteger('estimated_response_min')->nullable()->after('ai_services');
            $table->boolean('fire_people_trapped')->nullable()->after('estimated_response_min');
            $table->string('fire_smoke_level', 20)->nullable()->after('fire_people_trapped');
            $table->string('fire_building_type', 50)->nullable()->after('fire_smoke_level');
            $table->foreignId('assigned_service_id')->nullable()->after('fire_building_type')
                ->constrained('emergency_services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('assigned_service_id');
            $table->dropColumn([
                'ai_score', 'ai_summary', 'ai_services', 'estimated_response_min',
                'fire_people_trapped', 'fire_smoke_level', 'fire_building_type',
            ]);
        });
        Schema::dropIfExists('emergency_services');
    }
};
