<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeInChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            DB::statement("ALTER TABLE chat_messages MODIFY type ENUM('TEXT', 'VOICE', 'FILE') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            DB::statement("ALTER TABLE chat_messages MODIFY type ENUM('TEXT', 'VOICE') NOT NULL");
        });
    }
}
