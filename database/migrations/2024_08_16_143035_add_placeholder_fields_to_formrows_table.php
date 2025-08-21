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
        Schema::table(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
	        $table->string('placeholder', 64)->nullable()->after('slug');
	        $table->string('placeholder_getter_method', 64)->nullable()->after('slug');
	        $table->string('default_value_getter_method', 64)->nullable()->after('default_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
	        $table->dropColumn('placeholder');
	        $table->dropColumn('placeholder_getter_method');
	        $table->dropColumn('default_value_getter_method');
        });
    }
};
