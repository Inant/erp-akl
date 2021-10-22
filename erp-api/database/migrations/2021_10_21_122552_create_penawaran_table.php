<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePenawaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penawaran', function (Blueprint $table) {
            $table->increments('id');
            $table->string('no', 30);
            $table->date('tanggal_penawaran');
            $table->string('tipe_customer'); //tipe customer = retail / grosir / distributor
            $table->integer('customer_id');
            $table->boolean('is_closed')->nullable()->default(false);
            $table->double('total', 13, 2);
            $table->integer('site_id')->nullable();
            $table->text('alamat_kirim')->nullable();
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
        Schema::dropIfExists('penawaran');
    }
}
