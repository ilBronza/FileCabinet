<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormrowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('filecabinet.models.formrow.table'), function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('form_id');
            $table->foreign('form_id')->references('id')->on(config('filecabinet.models.form.table'));

            $table->string('name');
            $table->string('slug');

            $table->string('type');
            $table->boolean('required')->default(false);
            $table->boolean('repeatable')->default(false);

            $table->text('parameters')->nullable();
            $table->text('description')->nullable();

            $table->string('default_value', 1024)->nullable();

            $table->unsignedInteger('sorting_index')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('filecabinet.models.formrow.table'));
    }
}
