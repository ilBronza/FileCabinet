<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(config('filecabinet.models.dossier.table'), function (Blueprint $table) {
            $table->uuid('parent_id')->after('form_id')->nullable();

            $table->foreign('parent_id')->references('id')->on(config('filecabinet.models.dossier.table'));
        });

        Schema::table(config('filecabinet.models.filecabinet.table'), function (Blueprint $table) {
            $table->timestamp('populated_at')->after('populated_by')->nullable();

            //
        });

        Schema::table(config('filecabinet.models.dossier.table'), function (Blueprint $table) {
            $table->unsignedInteger('sorting_index')->after('parent_id')->nullable();
        });

        Schema::table(config('filecabinet.models.form.table'), function (Blueprint $table) {
            $table->unsignedInteger('sorting_index')->after('parent_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
