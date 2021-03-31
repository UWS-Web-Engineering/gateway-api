<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('serviceId');
            $table->foreign('serviceId')->references('id')->on('services');
            $table->string('method')->notNullable();
            $table->string('url')->notNullable();
            $table->integer('statusCode')->notNullable();
            $table->bigInteger('requestTime')->notNullable();
            $table->bigInteger('responseTime')->notNullable();
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
        Schema::dropIfExists('logs');
    }
}
