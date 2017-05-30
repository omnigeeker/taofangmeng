<?php

use \Laravel\Database\Schema;

class Create_First {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
	public function up()
	{
        // results
        Schema::create('results', function($table)
        {
            $table->increments('id');
            $table->string('username', 100);
            $table->string('module', 20);
            $table->string('step', 20)->nullable();
            $table->string('cache', 8000);
            $table->string('info', 8000);
            $table->timestamps();
        });
	}

	/**
	 * Revert the changes to the database.
	 *
	 * @return void
	 */
	public function down()
	{
		// results
        Schema::drop("results");
	}

}