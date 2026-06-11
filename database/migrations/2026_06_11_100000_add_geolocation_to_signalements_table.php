<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('localisation');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('video')->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'video']);
        });
    }
};
