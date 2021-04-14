<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilecabinetrowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filecabinetrows', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug');

            $table->string('type');
            $table->boolean('nullable')->default(false);

            $table->text('parameters')->nullable();

            $table->unsignedBigInteger('filecabinet_id');
            $table->foreign('filecabinet_id')->references('id')->on('filecabinets');

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
        Schema::dropIfExists('filecabinetrows');
    }
}
