<?php namespace Ryun\Module;

use Illuminate\Filesystem\Filesystem;

class ModuleCreator {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem
	 */
	protected $files;
	protected $directory;

	/**
	 * The building blocks of the module.
	 *
	 * @param  array
	 */
	protected $blocks = array(
		'SupportDirectories',
		'ServiceProvider',
		'ConfigFile',
		'RoutesFile',
	);

	/**
	 * Create a new module creator instance.
	 *
	 * @param  \Illuminate\Filesystem  $files
	 * @return void
	 */
	public function __construct(Filesystem $files)
	{
		$this->files = $files;
	}

	/**
	 * Create a new module stub.
	 *
	 * @param  $meta
	 * @param  string  $path
	 * @param  bool    $plain
	 * @return string
	 */
	public function create($meta, $path)
	{
		$this->directory = $this->createDirectory($meta, $path);

		foreach ($this->blocks as $block)
		{
			$this->{"write{$block}"}($meta, $this->directory);
		}

		return $this->directory;
	}

	/**
	 * Create the boilerplate directories for a module.
	 *
	 * @param  $meta
	 * @param  string  $directory
	 * @return void
	 */
	public function writeSupportDirectories($meta, $directory)
	{
		foreach (array('Public', 'Config', 'Controllers', 'Lang', 'Migrations', 'Views') as $support)
		{
			$path = $directory.'/'.$support;

			$this->files->makeDirectory($path, 0777, true);

			$this->files->put($path.'/.gitkeep', '');
		}
	}

	/**
	 * Write routes file
	 *
	 * @param  $meta
	 * @param  string  $directory
	 * @return void
	 */
	public function writeRoutesFile($meta, $directory)
	{
		$file = $directory.'/routes.php';
		$stub = $this->files->get(__DIR__.'/stubs/routes.stub');

		$this->files->put($file, $stub);
	}
	/**
	 * Write config file
	 *
	 * @param  $meta
	 * @param  string  $directory
	 * @return void
	 */
	public function writeConfigFile($meta, $directory)
	{
		$file = $directory.'/Config/config.php';
		$stub = $this->files->get(__DIR__.'/stubs/config.stub');
		$this->files->put($file, $stub);
	}

	/**
	 * Write service provider file
	 *
	 * @param  $meta
	 * @param  string  $directory
	 * @return void
	 */
	public function writeServiceProvider($meta, $directory)
	{
		$file = $this->files->get(__DIR__.'/stubs/serviceprovider.stub');
		$stub = $this->formatPackageStub($meta, $file);
		$provider = $directory.'/Module.php';
		$this->files->put($provider, $stub);
	}

	/**
	 * Replace the placeholders in the stub file
	 *
	 * @param  $meta
	 * @param  string  $stub
	 * @return string
	 */
	protected function formatPackageStub($meta, $stub)
	{
		foreach ($meta as $key => $value)
		{
			$stub = str_replace('{{'.snake_case($key).'}}', $value, $stub);
		}
		
		return $stub;
	}

	/**
	 * Create a directory for the module.
	 *
	 * @param  $meta
	 * @param  string  $path
	 * @return string
	 */
	protected function createDirectory($meta, $path)
	{
		$fullPath = $path.'/'.$meta['name'];

		if ( ! $this->files->isDirectory($fullPath))
		{
			$this->files->makeDirectory($fullPath, 0777, true);

			return $fullPath;
		}
		die("\n\nModule exists with that name!\n\n");
	}

}
