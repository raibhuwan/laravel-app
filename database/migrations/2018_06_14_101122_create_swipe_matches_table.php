<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSwipeMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('swipe_matches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->integer('a')->unsigned();
            $table->integer('b')->unsigned();
            $table->unique(['a', 'b']);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('a')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('b')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

        DB::unprepared("
        CREATE TRIGGER before_swipe_matches_insert
            BEFORE INSERT ON swipe_matches
            FOR EACH ROW 
        BEGIN
            DECLARE found_count INTEGER;
            DECLARE msg VARCHAR(255);
        SELECT 
            COUNT(*)
        INTO found_count FROM
            (SELECT 
                *
            FROM
                swipe_matches
            WHERE
                a = NEW.a AND b = NEW.b 
            UNION ALL 
            SELECT 
                *
            FROM
                swipe_matches
            WHERE
                a = NEW.b AND b = NEW.a) AS pairs;
            SET msg=CONCAT('Match ', NEW.a,' and ', NEW.b , ' already exists');		
            
            if  found_count > 0 then
               SIGNAL SQLSTATE '45000'
               SET MESSAGE_TEXT = msg; 
               end if;
        END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `before_swipe_matches_insert`');
        Schema::dropIfExists('swipe_matches');
    }
}
