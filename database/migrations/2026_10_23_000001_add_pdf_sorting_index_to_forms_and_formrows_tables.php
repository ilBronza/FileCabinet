<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(config('filecabinet.models.form.table'), function (Blueprint $table) {
            $table->unsignedInteger('pdf_sorting_index')->nullable()->after('sorting_index');
        });

        Schema::table(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
            $table->unsignedInteger('pdf_sorting_index')->nullable()->after('sorting_index');
        });
    }

    public function down(): void
    {
        Schema::table(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
            $table->dropColumn('pdf_sorting_index');
        });

        Schema::table(config('filecabinet.models.form.table'), function (Blueprint $table) {
            $table->dropColumn('pdf_sorting_index');
        });
    }
};
