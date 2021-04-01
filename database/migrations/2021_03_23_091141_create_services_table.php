<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNullable();
            $table->string('description');
            $table->string('key')->unique()->notNullable();
            $table->boolean('secure')->default(true)->notNullable();
            $table->string('domain')->notNullable();
            $table->string('port')->notNullable();
            $table->string('path')->notNullable();
            $table->boolean('active')->default(true)->notNullable();
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
        Schema::dropIfExists('services');
    }
}
