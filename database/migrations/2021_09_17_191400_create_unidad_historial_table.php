<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnidadHistorialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unidad_historial', function (Blueprint $table) {
            $table->id();
            $table->string('id_wialon');
            $table->string('name');
            $table->string('latitud');
            $table->string('longitud');
            $table->string('placa');
            $table->string('lastMessage');
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
        Schema::dropIfExists('unidad_historial');
    }
}
