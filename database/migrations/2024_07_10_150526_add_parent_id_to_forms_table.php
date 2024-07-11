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
        Schema::table(config('filecabinet.models.form.table'), function (Blueprint $table) {
            $table->uuid('parent_id')->after('name')->nullable();

            $table->foreign('parent_id')->references('id')->on(config('filecabinet.models.form.table'));
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
