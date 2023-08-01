<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppleInAppSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apple_in_app_subscriptions', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('plan_subscription_id')->unsigned();
            $table->string('product_id');

            $table->text('receipt_data');
            $table->string('original_transaction_id');
            $table->string('transaction_id');
            $table->dateTime('purchase_date');
            $table->dateTime('expires_date');
            $table->dateTime('original_purchase_date');
            $table->tinyInteger('auto_renew_status')->default(0);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('plan_subscription_id')->references('id')->on('plan_subscriptions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('apple_in_app_subscriptions');
    }
}
