<?php namespace Ryun\Module\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use DB, View, File, Str;

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

	protected $package = 'ryun/module';
	protected $publishPath;
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	/**
	 * Create a new key generator command.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		parent::__construct();

		$this->files = $files;
		$this->publishPath = app_path().'/config/'.$this->package.'/config.php';
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		//Publish config
		//Set config values
		//Create folder(s)
		//Publish config, assets, or views for package
	
		list($path, $contents) = $this->getConfigFile();

		//Grab config values
		$defaultPath = $this->laravel['config']['ryun/module::path'];
		$defaultNamespace = $this->laravel['config']['ryun/module::namespace'];
		
		//Ask config values
		$path = $this->ask('Base path that hold all our modules?', $defaultPath);
		$namespace = $this->ask('Root namespace for modules folder?', $defaultPath);
		
		//set up find & replace arrays
		$find = [$defaultPath, $defaultNamespace];
		$replace = [$path, $namespace];

		//Replace strings
		$contents = str_replace($find, $replace, $contents);

		//Write file
		$this->files->put($path, $contents);

		$this->laravel['config']['module::path'] = $path;
		$this->laravel['config']['module::namespace'] = $namespace;

		$this->info('Modules path set to: '.$path);
		$this->info('Modules namespace set to: '.$namespace);
	}

	/**
	 * Get the key file and contents.
	 *
	 * @return array
	 */
	protected function getConfigFile()
	{
		$env = $this->option('env') ? $this->option('env').'/' : '';

		//Findpackage dir
		
		//$contents = $this->files->get($path = base_path()."/vendor/Ryun/Module/src/config/config.php");
		$contents = $this->files->get($this->publishPath);


		return array($this->publishPath, $contents);
	}
	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('table', InputArgument::REQUIRED, 'The name of the database table the file fields will be added to.'),
			array('attachment', InputArgument::REQUIRED, 'The name of the corresponding stapler attachment.'),
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

	/**
	 * Create a new migration.
	 *
	 * @return void
	 */
	public function createMigration()
	{
		$data = ['table' => $this->argument('table'), 'attachment' => $this->argument('attachment')];
		$prefix = date('Y_m_d_His');
		$path = app_path() . '/database/migrations';

		if (!is_dir($path)) mkdir($path);

		$fileName  = $path . '/' . $prefix . '_add_' . $data['attachment'] . '_fields_to_' . $data['table'] . '_table.php';
		$data['className'] = 'Add' . ucfirst($data['attachment']) . 'FieldsTo' . ucfirst(Str::camel($data['table'])) . 'Table';

		// Save the new migration to disk using the stapler migration view.
		$migration = View::make('stapler::migration', $data)->render();
		File::put($fileName, $migration);

		// Dump the autoloader and print a created migration message to the console.
		$this->call('dump-autoload');
		$this->info("Created migration: $fileName");
	}
}