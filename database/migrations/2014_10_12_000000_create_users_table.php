<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 36)->unique();
            $table->string('name', '100')->nullable();
            $table->string('password')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('phone')->nullable();
            $table->unique(['country_code', 'phone']);
            $table->tinyInteger('phone_verified')->default(0);
            $table->string('email')->unique()->nullable();
            $table->tinyInteger('email_verified')->default(0);
            $table->rememberToken();
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->date('dob');
            $table->enum('role', ['BASIC_USER', 'ADMIN_USER'])->default('BASIC_USER');
            $table->string('about_me', 255)->nullable();
            $table->text('school')->nullable();
            $table->text('work')->nullable();
            $table->tinyInteger('is_active')->default(0);

            $table->string('provider', 20)->nullable();
            $table->string('provider_id')->unique()->nullable();
            $table->string('fcm_registration_id')->unique()->nullable();
            $table->text('access_token')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
