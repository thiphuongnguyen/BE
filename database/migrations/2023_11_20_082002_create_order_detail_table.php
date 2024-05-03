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
        Schema::create('order_detail', function (Blueprint $table) {
            $table->id('order_detail_id');
            $table->unsignedBigInteger('order_id')->index();
            $table->foreign('order_id')
                ->references('order_id')
                ->on('order')
                ->onDelete('cascade');
            $table->unsignedBigInteger('color_id')->index();
            $table->foreign('color_id')
                ->references('color_id')
                ->on('colors')
                ->onDelete('cascade');
            $table->integer('product_id');
            $table->string('product_image');
            $table->string('product_name');
            $table->double('product_price');
            $table->integer('product_sales_quantity');
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
        Schema::dropIfExists('order_detail');
    }
};
