<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->integer('user_id')->unsigned()->unique();
            $table->integer('search_distance')->default(10);
            $table->enum('distance_in', ['MI','KM'])->default('MI');
            $table->integer('show_ages_min')->default(18);
            $table->integer('show_ages_max')->default(35);
            $table->enum('interested_in', ['FRIENDSHIP', 'RELATIONSHIP', 'CASUAL_MEETUP'])->default('RELATIONSHIP');
            $table->enum('date_with', ['MALE', 'FEMALE' ,'BOTH'])->default('FEMALE');
            $table->tinyInteger('privacy_show_distance')->default(1);
            $table->tinyInteger('privacy_show_age')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
