<?php

use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modules', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('slug', 50);
			$table->text('name');
			$table->text('description');
			$table->string('version', 20);
			$table->string('category', 100);
		    // STATUS_DISABLED  = -1;
		    // STATUS_INSTALL   = 0;
		    // STATUS_INSTALLED = 1;
		    // STATUS_UPGRADE   = 2;
			$table->boolean('status'); //-1 disable, 1 enabled
			$table->integer('updated_on')->nullable();
			$table->unique('slug');
			$table->index('status');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modules');
	}

}