<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('filecabinet.models.form.table'), function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('slug');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('interventions')->nullable();

            $table->boolean('repeatable')->nullable();

            $table->uuid('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on(config('category.models.category.table'));

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
        Schema::dropIfExists(config('filecabinet.models.form.table'));
    }
}
