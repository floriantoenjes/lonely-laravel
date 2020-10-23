<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLonelySettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_lonely_settings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('latitude');
            $table->string('longitude');
            $table->string('city');
            $table->timestamp('lonely_since')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_lonely_settings');
    }
}
