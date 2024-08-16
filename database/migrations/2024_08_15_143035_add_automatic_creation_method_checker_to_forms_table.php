<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(config('filecabinet.models.form.table'), function (Blueprint $table) {
			$table->boolean('automatically_creatable')->after('description')->nullable();
			$table->string('automatic_creation_checker_method')->after('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('filecabinet.models.form.table'), function (Blueprint $table) {
			$table->dropColumn('automatically_creatable')->nullable();
			$table->dropColumn('automatic_creation_checker_method')->nullable();
        });
    }
};
