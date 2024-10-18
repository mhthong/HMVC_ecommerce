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
        Schema::table('pages', function (Blueprint $table) {
            // Thêm cột slider_id và thiết lập khóa ngoại liên kết với bảng sliders
            $table->unsignedBigInteger('slider_id')->nullable();
            $table->foreign('slider_id')->references('id')->on('sliders')->onDelete('cascade');
        });

        Schema::table('posts', function (Blueprint $table) {
            // Thêm cột slider_id và thiết lập khóa ngoại liên kết với bảng sliders
            $table->unsignedBigInteger('slider_id')->nullable();
            $table->foreign('slider_id')->references('id')->on('sliders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['slider_id']);
            $table->dropColumn('slider_id');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['slider_id']);
            $table->dropColumn('slider_id');
        });
    }
};
