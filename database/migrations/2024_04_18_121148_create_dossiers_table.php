<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDossiersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('filecabinet.models.dossier.table'), function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('form_id');
            $table->foreign('form_id')->references('id')->on(config('filecabinet.models.form.table'));

            $table->nullableUuidMorphs('dossierable');

            $table->unsignedBigInteger('populated_by')->nullable()->index();

            $table->timestamp('populated_at')->nullable();
            $table->timestamp('must_be_updated_at')->nullable();

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
        Schema::dropIfExists(config('filecabinet.models.dossier.table'));
    }
}
