<?php namespace Ryun\Module\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MigrateCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:migrate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Performs migrations for the Module package.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$connection = $this->laravel['config']['ryun/module::db.connection'];
		$this->call('migrate', array('--package' => 'ryun/module', '--database' => $connection));
	}
}