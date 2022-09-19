<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilecabinetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filecabinets', function (Blueprint $table) {
            $table->string('name');
            $table->string('slug');

            $table->boolean('repeatable')->default(false);
            $table->boolean('multiple')->default(false);

            $table->unsignedSmallInteger('validity_days')->nullable();
            $table->unsignedSmallInteger('validity_months')->nullable();
            $table->unsignedTinyInteger('validity_months_starting-date')->nullable();

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
        Schema::dropIfExists('filecabinets');
    }
}
