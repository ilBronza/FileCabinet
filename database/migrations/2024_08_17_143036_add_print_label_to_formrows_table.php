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
		    $table->boolean('pdf_print_label')->nullable()->after('pdf_title');
		    $table->string('pdf_label')->nullable()->after('pdf_title');
	    });

	    Schema::table(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
		    $table->boolean('pdf_print_label')->nullable()->after('pdf_title');
		    $table->string('pdf_label')->nullable()->after('pdf_title');
	    });
    }

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table(config('filecabinet.models.form.table'), function (Blueprint $table) {
			$table->dropColumn('pdf_print_label');
			$table->dropColumn('pdf_label');
		});

		Schema::table(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
			$table->dropColumn('pdf_print_label');
			$table->dropColumn('pdf_label');
		});
	}
};
