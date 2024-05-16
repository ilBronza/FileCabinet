<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDossierrowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('filecabinet.models.dossierrow.table'), function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('dossier_id');
            $table->foreign('dossier_id')->references('id')->on(config('filecabinet.models.dossier.table'));

            $table->ulid('formrow_id');
            $table->foreign('formrow_id')->references('id')->on(config('filecabinet.models.formrow.table'));

            $table->timestamp('timestamp')->nullable();
            $table->text('text')->nullable();
            $table->string('string')->nullable();
            $table->decimal('decimal', 16, 4)->nullable();
            $table->boolean('boolean')->nullable();

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
        Schema::dropIfExists(config('filecabinet.models.dossierrow.table'));
    }
}
