<?php

class Create_First_Tables {

	/**
	 * Make changes to the database.
	 *
	 * @return void
	 */
    public function up()
    {
        // 用户反馈
        // callbacks
        Schema::create('callbacks', function($table) {
            $table->increments('id');
            $table->string('username', 100);
            $table->string('content', 1000);
            $table->timestamps();
        });

        // 用户每年纪录
        //
        Schema::create('year_profiles', function($table) {
            $table->increments('id');
            $table->string('username', 100);
            $table->string('guid', 36);
            $table->integer('year');
            $table->text('detail');
            $table->timestamps();
        });

        // 通关纪录
        Schema::create('victor_profiles', function($table) {
            $table->increments('id');
            $table->string('username', 100);
            $table->string('guid', 36);
            $table->integer('year');
            $table->text('detail');
            $table->string('content');
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
        //
        Schema::drop('victor_profiles');
        Schema::drop('year_profiles');
        Schema::drop('callbacks');
    }

}