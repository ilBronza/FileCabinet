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
	    Schema::table(config('filecabinet.models.filecabinetTemplate.table'), function (Blueprint $table) {
		    $table->string('pdf_image', 255)->nullable()->after('pdf_template');
		    $table->string('pdf_title', 255)->nullable()->after('pdf_template');
		    $table->text('pdf_description')->nullable()->after('pdf_template');
		    $table->boolean('pdf_show_menu')->nullable()->after('pdf_template');
		    $table->boolean('pdf_print_fields_when_empty')->nullable()->after('pdf_template');
	    });

	    Schema::table(config('filecabinet.models.form.table'), function (Blueprint $table) {
		    $table->string('pdf_title', 255)->nullable()->after('description');
		    $table->text('pdf_description')->nullable()->after('description');
		    $table->boolean('pdf_show_menu')->nullable()->after('description');
		    $table->boolean('pdf_print_fields_when_empty')->nullable()->after('description');
	    });

	    Schema::table(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
		    $table->string('pdf_title', 255)->nullable()->after('description');
		    $table->text('pdf_description')->nullable()->after('description');
		    $table->boolean('pdf_show_menu')->nullable()->after('description');
		    $table->boolean('pdf_print_fields_when_empty')->nullable()->after('description');
	    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
	    Schema::table(config('filecabinet.models.filecabinetTemplate.table'), function (Blueprint $table) {
		    $table->dropColumn('pdf_title');
		    $table->dropColumn('pdf_description');
		    $table->dropColumn('pdf_show_menu');
		    $table->dropColumn('pdf_print_fields_when_empty');
	    });

	    Schema::table(config('filecabinet.models.form.table'), function (Blueprint $table) {
		    $table->dropColumn('pdf_title');
		    $table->dropColumn('pdf_description');
		    $table->dropColumn('pdf_show_menu');
		    $table->dropColumn('pdf_print_fields_when_empty');
	    });

	    Schema::table(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
		    $table->dropColumn('pdf_title');
		    $table->dropColumn('pdf_description');
		    $table->dropColumn('pdf_show_menu');
		    $table->dropColumn('pdf_print_fields_when_empty');
	    });
	}
};
