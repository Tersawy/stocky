<?php

use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_return_details', function (Blueprint $table) {
            $table->id();

            $table->integer("cost");
            $table->integer("quantity");
            $table->integer('tax')->default(0);
            $table->tinyInteger("tax_method")->default(Product::TAX_EXCLUSIVE); // 0 Exclusive, 1 Inclusive
            $table->integer('discount')->default(0);
            $table->tinyInteger("discount_method")->default(Product::DISCOUNT_FIXED); // 0 Fixed, 1 Percent

            $table->bigInteger('variant_id')->nullable();

            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->unsignedBigInteger('purchase_return_id');
            $table->foreign('purchase_return_id')->references('id')->on('purchase_returns')->onDelete('cascade');

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
        Schema::dropIfExists('purchase_return_details');
    }
}