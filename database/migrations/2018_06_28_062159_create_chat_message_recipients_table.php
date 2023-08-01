<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatMessageRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_message_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->integer('recipient_id')->unsigned(); // The id of the recipient (one who receives the message)
            $table->integer('chat_message_id')->unsigned();
            $table->unique(['recipient_id', 'chat_message_id']);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('chat_message_id')->references('id')->on('chat_messages')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_message_recipients');
    }
}
