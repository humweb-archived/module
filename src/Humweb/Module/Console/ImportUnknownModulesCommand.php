<?php namespace Humweb\Module\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ImportUnknownModulesCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:scan';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scan for installable modules.';
    /**
     * @var
     */
    protected $app;


    /**
     * Create a new command instance.
     *
     */
	public function __construct($app)
	{
		parent::__construct();

        $this->app = $app;
    }

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
        $installableModules = $this->app['modules']->getManager()->importUnknown();

        //Report count
        $this->info('Unknown Modules found: '.count($installableModules));

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array();
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}
}