<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParametroZonasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parametro_zonas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parametro_id');
            $table->unsignedBigInteger('zona_id');
            $table->timestamps();

            $table->foreign('parametro_id')->references('id')->on('parametros');
            $table->foreign('zona_id')->references('id')->on('zonas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parametro_zonas');
    }
}
