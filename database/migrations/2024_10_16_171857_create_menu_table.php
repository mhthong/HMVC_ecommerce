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
        Schema::create('menu', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('link')->nullable();
            $table->unsignedBigInteger('main_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('slug_id')->nullable()->unique();
            $table->timestamps();

            $table->foreign('main_id')->references('id')->on('menu_main')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('menu')->onDelete('cascade');
            $table->foreign('slug_id')->references('id')->on('slugs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu');
    }
};
