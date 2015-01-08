<?php namespace Humweb\Module\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallModuleCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:install';

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
        $module = $this->argument('name');
        if ($this->app['modules']->install($module))
        {
            $this->info('Module "'.$module.'" installed successfully');
        }
        else {
            $this->info('Module "'.$module.'" was not installed successfully');
        }
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
            array('name', InputArgument::REQUIRED, 'The name of the module.'),
            );
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