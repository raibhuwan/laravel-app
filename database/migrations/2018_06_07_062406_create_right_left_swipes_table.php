<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRightLeftSwipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('right_left_swipes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->integer('a')->unsigned();
            $table->integer('b')->unsigned();
            $table->unique(['a', 'b']);
            $table->enum('swipe_type', ['LIKE', 'NOPE', 'SUPERLIKE'])->default('NOPE');
            $table->dateTime('expired_at')->nullable();
//            $table->longText('swipe_record')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('a')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('b')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('right_left_swipes');
    }
}
