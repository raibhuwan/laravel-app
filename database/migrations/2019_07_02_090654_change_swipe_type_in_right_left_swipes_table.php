<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSwipeTypeInRightLeftSwipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('right_left_swipes', function (Blueprint $table) {
            DB::statement("ALTER TABLE right_left_swipes MODIFY swipe_type ENUM('LIKE', 'NOPE', 'SUPER_LIKE') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('right_left_swipes', function (Blueprint $table) {
            DB::statement("ALTER TABLE right_left_swipes MODIFY swipe_type ENUM('LIKE', 'NOPE', 'SUPERLIKE') NOT NULL");
        });
    }
}
