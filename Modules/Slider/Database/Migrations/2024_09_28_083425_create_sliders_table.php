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
        // Tạo bảng sliders
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('key', 120);
            $table->string('description', 255)->nullable();
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        // Tạo bảng slider_items và thiết lập khóa ngoại với sliders
        Schema::create('slider_items', function (Blueprint $table) {
            $table->id();

            // Thiết lập khóa ngoại slider_id để liên kết với bảng sliders
            $table->unsignedBigInteger('slider_id');
            $table->foreign('slider_id')->references('id')->on('sliders')->onDelete('cascade');

            $table->string('title', 255);
            $table->string('image', 255);
            $table->string('link', 255)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
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
        // Đảm bảo rằng khóa ngoại trong bảng slider_items bị xóa trước khi xóa bảng chính
        Schema::table('slider_items', function (Blueprint $table) {
            $table->dropForeign(['slider_id']);
        });

        // Xóa bảng sliders và slider_items
        Schema::dropIfExists('slider_items');
        Schema::dropIfExists('sliders');
    }
};
