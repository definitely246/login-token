<?php

use Illuminate\Database\Migrations\Migration;

class CreateLoginTokenTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
	    Schema::create('def246_login_tokens', function($table)
	    {
			$table->increments('id');
			$table->string('token_string');
			$table->unsignedInteger('identifiable_id')->nullable();
			$table->string('identifiable_type')->nullable();
			$table->datetime('expires_at')->nullable();
			$table->timestamps();

			$table->unique('token_string');
	    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
    Schema::drop('def246_login_tokens');
	}

}