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
        Schema::create('providers', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("price_list_id");
            $table->foreign('price_list_id')
                ->references('id')
                ->on('price_lists')
                ->onDelete('cascade');
            $table->uuid("company_id");
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');
            $table->uuid("leg_id");
            $table->foreign('leg_id')
                ->references('id')
                ->on('legs')
                ->onDelete('cascade');
            $table->unsignedDecimal("price", $precision = 10, $scale = 2);
            $table->timestamp("flight_start");
            $table->timestamp("flight_end");
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
        Schema::dropIfExists('providers');
    }
};
