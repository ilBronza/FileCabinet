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
        Schema::create('dossierrows', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('dossier_id');
            $table->foreign('dossier_id')->references('id')->on('dossiers');

            $table->unsignedBigInteger('filecabinetrow_id');
            $table->foreign('filecabinetrow_id')->references('id')->on('filecabinetrows');

            $table->timestamp('timestamp')->nullable();
            $table->text('text');
            $table->string('string');
            $table->float(16, 4)->nullable();
            $table->boolean('boolean')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('filecabinetrows', function (Blueprint $table)
        {
            $table->string('default_value', 1024)->nullable();
        });
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dossierrows');

        Schema::table('filecabinetrows', function (Blueprint $table)
        {
            $table->dropColumn('default_value');
        });
    }
}
