<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->integer('reported_by')->unsigned();
            $table->integer('reported_to')->unsigned();
            $table->string('reason');

            $table->softDeletes();
            $table->timestamps();

            $table->foreign('reported_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('reported_to')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_users');
    }
}
