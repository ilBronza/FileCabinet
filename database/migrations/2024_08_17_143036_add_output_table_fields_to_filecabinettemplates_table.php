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

		    $table->boolean('table_show')->nullable();
		    $table->string('table_title', 255)->nullable();
		    $table->text('table_description')->nullable();
	    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
	    Schema::table(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
		    $table->dropColumn('table_show');
		    $table->dropColumn('table_title');
		    $table->dropColumn('table_description');
	    });
	}
};
