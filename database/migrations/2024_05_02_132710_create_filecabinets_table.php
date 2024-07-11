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
        Schema::create(config('filecabinet.models.filecabinet.table'), function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('parent_id')->nullable();

            $table->uuid('category_id');
            $table->foreign('category_id')->references('id')->on(config('category.models.category.table'));

            $table->nullableUuidMorphs('filecabinetable', 'filecabinet_filecabinetable');

            $table->uuid('populated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table(config('filecabinet.models.filecabinet.table'), function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on(config('filecabinet.models.filecabinet.table'));
        });

        Schema::create(config('filecabinet.models.dossierFilecabinet.table'), function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('filecabinet_id');
            $table->foreign('filecabinet_id')->references('id')->on(config('filecabinet.models.filecabinet.table'));

            $table->uuid('dossier_id');
            $table->foreign('dossier_id')->references('id')->on(config('filecabinet.models.dossier.table'));

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
        Schema::dropIfExists(config('filecabinet.models.dossierFilecabinet.table'));
        Schema::dropIfExists(config('filecabinet.models.filecabinet.table'));
    }
};
