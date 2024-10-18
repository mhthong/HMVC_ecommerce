<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id()->unsigned();
            $table->string('name', 120);
            $table->string('slug', 120)->unique()->nullable();
            $table->integer('user_id')->references('id')->on('users');
            $table->string('status', 60)->default('published');
            $table->string('title', 120)->nullable();
            $table->text('content')->nullable();
            $table->string('image', 120)->nullable();
            $table->text('target')->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('posts');

    }
}
