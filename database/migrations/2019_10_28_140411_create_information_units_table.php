<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformationUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('information_units', function (Blueprint $table) {
            $table->Increments('id');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->integer('jumlah_awal');
            $table->integer('satuan_awal_id')->unsigned();
            $table->foreign('satuan_awal_id')->references('id')->on('units')->onDelete('cascade');
            $table->integer('jumlah_akhir');
            $table->integer('satuan_akhir_id')->unsigned();
            $table->foreign('satuan_akhir_id')->references('id')->on('units')->onDelete('cascade');
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
        Schema::dropIfExists('information_units');
    }
}
