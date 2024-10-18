<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('name')->unique();
            $table->text('shortdescription')->nullable();
            $table->text('description')->nullable();
            $table->text('content')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('priceoff', 10, 2);
            $table->string('image'); // Thêm cột image cho ảnh chính
            $table->tinyInteger('is_featured')->default(0);
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
        Schema::dropIfExists('products');
    }
};
