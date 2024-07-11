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
        Schema::create(config('filecabinet.models.filecabinetTemplate.table'), function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on(config('category.models.category.table'));

            $table->text('models')->nullable();

            $table->unsignedInteger('sorting_index')->nullable();

            $table->boolean('force_consecutiveness')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table(config('filecabinet.models.filecabinet.table'), function (Blueprint $table) {
            $table->uuid('filecabinet_template_id')->nullable();
            $table->foreign('filecabinet_template_id')->references('id')->on(config('filecabinet.models.filecabinetTemplate.table'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('filecabinet.models.filecabinet.table'), function (Blueprint $table) {
            $table->dropIndex(['filecabinet_template_id']);
            $table->dropColumn('filecabinet_template_id');
        });

        Schema::dropIfExists(config('filecabinet.models.filecabinetTemplate.table'));
    }
};
