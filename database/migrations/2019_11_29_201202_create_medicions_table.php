<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->double('valor');
            $table->dateTime('fecha');
            $table->unsignedBigInteger('parametro_zona_id');
            $table->timestamps();

            $table->foreign('parametro_zona_id')->references('id')->on('parametro_zonas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicions');
    }
}
