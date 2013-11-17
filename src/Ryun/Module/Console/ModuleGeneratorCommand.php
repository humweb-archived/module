<?php namespace Ryun\Module\Console;

use Illuminate\Console\Command;
use Ryun\Module\ModuleCreator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleGeneratorCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'module:make';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new module';

	protected $creator;

	/**
	 * Create a new make workbench command instance.
	 *
	 * @param  App\Modules\Workbench\ModuleCreator  $creator
	 * @return void
	 */
	public function __construct(ModuleCreator $creator)
	{
		parent::__construct();

		$this->creator = $creator;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$name = studly_case($this->argument('name'));

		$config = $this->laravel['config']['workbench'];

		$meta = [
			'name'   => $name,
			'author' => $config['name'],
			'email'  => $config['email'],
			'basepath' => $this->laravel['config']['module::path'],
			'namespace' => $this->laravel['config']['module::namespace']
			];

		$workbench = $this->runCreator($meta);

		$this->info("\n\nModule \"".$name."\" created!");
	}

	/**
	 * Run the package creator class for a given Package.
	 *
	 * @param  array $meta
	 * @return string
	 */
	protected function runCreator($meta)
	{
		$path = $meta['basepath'];

		return $this->creator->create($meta, $path);
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


}
