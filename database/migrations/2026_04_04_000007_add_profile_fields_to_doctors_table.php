<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table): void {
            $table->string('profile_photo_path')->nullable()->after('license_no');
            $table->unsignedTinyInteger('experience_years')->default(1)->after('profile_photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table): void {
            $table->dropColumn(['profile_photo_path', 'experience_years']);
        });
    }
};
