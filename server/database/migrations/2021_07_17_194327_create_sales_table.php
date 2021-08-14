<?php

use App\Helpers\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->default("SL_1110");

            $table->float('tax', 10, 0)->default(0);
            $table->float('discount', 10, 0)->default(0);
            $table->tinyInteger("discount_method")->default(Constants::DISCOUNT_FIXED); // 0 Fixed, 1 Percent
            $table->tinyInteger('status')->default(Constants::SALE_COMPLETED);
            $table->tinyInteger('payment_status')->default(Constants::PAYMENT_STATUS_UNPAID);
            $table->boolean('is_pos')->nullable()->default(0);
            $table->float('shipping', 10, 0)->default(0);
            $table->float('total_price', 10, 0)->default(0);
            $table->float('paid', 10, 0)->default(0);

            $table->text('note')->nullable();
            $table->date('date')->default(date('Y-m-d'));

            $table->unsignedBigInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');

            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('restrict');

            $table->softDeletes();
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
        Schema::dropIfExists('sales');
    }
}
