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
        Schema::create('legs', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("price_list_id");
            $table->foreign('price_list_id')
                ->references('id')
                ->on('price_lists')
                ->onDelete('cascade');
            $table->uuid("from_planet_id");
            $table->foreign('from_planet_id')
                ->references('id')
                ->on('planets')
                ->onDelete('cascade');
            $table->uuid("to_planet_id");
            $table->foreign('to_planet_id')
                ->references('id')
                ->on('planets')
                ->onDelete('cascade');
            $table->unsignedBigInteger("distance");
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
        Schema::dropIfExists('legs');
    }
};
