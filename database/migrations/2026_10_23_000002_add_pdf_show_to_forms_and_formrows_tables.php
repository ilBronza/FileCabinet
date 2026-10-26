<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['form', 'formrow'] as $model) {
            Schema::table(config("filecabinet.models.{$model}.table"), function (Blueprint $table) {
                $table->boolean('pdf_show')->nullable()->default(null);
            });
        }
    }

    public function down(): void
    {
        foreach (['formrow', 'form'] as $model) {
            Schema::table(config("filecabinet.models.{$model}.table"), function (Blueprint $table) {
                $table->dropColumn('pdf_show');
            });
        }
    }
};
