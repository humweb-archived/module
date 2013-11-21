<?php namespace Humweb\Module\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SetupCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Initial migration and folder setup for the module package.';

	protected $package = 'humweb/module';
	protected $configPath;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		parent::__construct();

		$this->files = $files;
		$this->configPath = base_path().'/vendor/'.$this->package.'/src/config';
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		//Check folder
		//check connection
		
		$inputPath = $this->argument('path');
		$inputNamespace = $this->argument('namespace');
		
		//Set config values
		list($path, $namespace) = $this->setConfigValues($inputPath, $inputNamespace);
		
		//Create modules folder
		$this->createBaseFolder(base_path().'/'.$path);

		//Publish
		$this->call('config:publish', array('package' => $this->package));

		$this->laravel['config']['module::path'] = $path;
		$this->laravel['config']['module::namespace'] = $namespace;

		$this->info('Modules path set to: '.$path);
		$this->info('Modules namespace set to: '.$namespace);
		$this->info('');
		$this->info('Now add a psr-0 autoload entry for "'.$namespace.'" in the composer.json file."');

	}

	protected function runMigration($path = null)
	{
		$connection = $this->laravel['config']['module::db.connection'];
		$this->call('migrate', array('--package' => 'humweb/module', '--database' => $connection));
	}

	protected function createBaseFolder($path = null)
	{
		if ( ! $this->files->isDirectory($path))
		{
			$this->files->makeDirectory($path, 0777, true);

			return $path;
		}
		die("\n\nChoose another name, a folder with that name exists!\n\n");
	}

	protected function setConfigValues($path = null, $namespace = null)
	{
		$baseConfigFile = $this->configPath.'/config.php';

		//Get contents
		$contents = $this->files->get($baseConfigFile);

		//Grab config values
		$defaultPath = $this->laravel['config']['module::path'];
		$defaultNamespace = $this->laravel['config']['module::namespace'];

		//Ask config values
		$path = $path ?: $this->ask('Base path for modules: '.base_path().'/', $defaultPath);
		$namespace = $namespace ?: $this->ask('Root namespace for modules folder?', $defaultNamespace);
		
		//Setup find & replace arrays
		$find = [$defaultPath, $defaultNamespace];
		$replace = [$path, $namespace];

		//Replace strings
		$contents = str_replace($find, $replace, $contents);

		//Write file
		$this->files->put($baseConfigFile, $contents);

		return $replace;
	}
	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('path', InputArgument::OPTIONAL, 'The root path realative to: '.base_path().'/'),
			array('namespace', InputArgument::OPTIONAL, 'The root namespace for the modules.'),
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