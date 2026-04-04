<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinic_applications', function (Blueprint $table): void {
            $table->string('logo_path')->nullable()->after('license_document_path');
        });
    }

    public function down(): void
    {
        Schema::table('clinic_applications', function (Blueprint $table): void {
            $table->dropColumn('logo_path');
        });
    }
};
