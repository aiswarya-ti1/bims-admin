<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLogSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_log_sessions', function (Blueprint $table) {
            $table->increments('Log_ID');
			$table->unsignedInteger('user_ID');
		   $table->foreign('user_ID')->references('User_ID')->on('logins')->onDelete('cascade');
		   $table->datetime('Log_Intime');
		   $table->datetime('Log_Ottime');
		   $table->string('Log_IP');
		   $table->string('Log_Status');
		   	 $table->string('Log_St_Desc');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_log_sessions');
    }
}
