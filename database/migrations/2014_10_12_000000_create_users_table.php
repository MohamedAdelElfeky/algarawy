<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('national_id')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('location');
            $table->string('governorate');
            $table->string('city');
            $table->string('area');
            $table->date('birth_date');
            $table->string('national_card_image_front')->nullable();
            $table->string('national_card_image_back')->nullable();
            $table->json('card_images')->nullable();
            $table->boolean('registration_confirmed')->default(false);
            $table->string('avatar')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
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
};
