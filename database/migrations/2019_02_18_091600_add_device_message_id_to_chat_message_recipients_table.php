<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\ChatMessageRecipient;
use Ramsey\Uuid\Uuid;

class AddDeviceMessageIdToChatMessageRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_message_recipients', function (Blueprint $table) {
            $table->string('device_message_id', 36);
        });

        $existing_threads = ChatMessageRecipient::get();

        foreach($existing_threads as $threads)
        {
            $threads->device_message_id = 'n-'.Uuid::uuid4();
            $threads->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_message_recipients', function (Blueprint $table) {
            $table->dropColumn('device_message_id');
        });
    }
}
