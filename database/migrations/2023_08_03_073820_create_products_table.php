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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');
            $table->string('product_name')->nullable();
            $table->string('product_slug')->nullable();
            $table->string('food_type')->default('veg');
            $table->string('product_type')->default('domestic');
            $table->text('description')->nullable();
            $table->bigInteger('product_catid')->unsigned();
            $table->bigInteger('product_subcatid')->unsigned();
            $table->integer('blocked')->default(1);
            $table->timestamps();

            $table->foreign('product_catid')->references('id')->on('product_category')->onDelete('cascade');
            $table->foreign('product_subcatid')->references('id')->on('product_sub_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
