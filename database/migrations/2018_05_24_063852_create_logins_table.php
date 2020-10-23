<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoginsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logins', function (Blueprint $table) {
            $table->increments('User_ID');
			$table->string('User_Name');
			$table->string('User_Login');
			$table->string('User_Password');
			$table->integer('Mall_ID');			
			$table->integer('Role_ID');
			$table->dateTime('User_CrDate');
		 	$table->integer('User_RegSource');			
			$table->integer('User_Status');			
			$table->string('User_Image');
			$table->string('User_Email');			
			$table->integer('address_id');
			
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logins');
    }
}
