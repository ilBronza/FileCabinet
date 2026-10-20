<?php

use IlBronza\FileCabinet\Offline\OfflineSync;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() : void
    {
        Schema::create(OfflineSync::TABLE, function (Blueprint $table) {
            $table->string('id', 64)->primary();
            $table->string('request_hash', 64);
            $table->longText('response');
            $table->timestamp('created_at');
        });
    }

    public function down() : void
    {
        Schema::dropIfExists(OfflineSync::TABLE);
    }
};
