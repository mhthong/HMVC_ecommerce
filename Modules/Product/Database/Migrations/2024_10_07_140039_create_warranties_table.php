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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Customer name
            $table->string('email')->unique(); // Customer email
            $table->string('phone')->unique(); // Customer phone number
            $table->string('address'); // Customer address
            $table->string('city'); // Customer city
            $table->string('district'); // Customer district
            $table->string('province'); // Customer province
            $table->timestamps();
        });

        Schema::create('warranties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Foreign key for product
            $table->string('warranty_code')->unique(); // Encrypted or unique warranty code
            $table->enum('status', ['active', 'pending', 'clear'])->default('pending'); // Status of the warranty
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('cascade'); // Foreign key for customer, nullable
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
        Schema::dropIfExists('warranties');
        Schema::dropIfExists('customers');

    }
};
