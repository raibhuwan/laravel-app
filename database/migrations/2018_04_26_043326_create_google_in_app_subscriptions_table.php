<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoogleInAppSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('google_in_app_subscriptions', function ($table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->integer('user_id')->unsigned();
            $table->integer('plan_subscription_id')->unsigned();
            $table->string('orderId',100)->unique();
            $table->string('packageName',100);
            $table->string('productId',100);
            $table->dateTime('purchaseTime');
            $table->text('purchaseToken');
            $table->tinyInteger('autoRenewing')->default(0);
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
        Schema::drop('google_in_app_subscriptions');
    }
}
