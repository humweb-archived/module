<?php namespace Ryun\Module;

use Illuminate\Filesystem\Filesystem;

class FileLoader {

	/**
	 * The filesystem instance.
	 *
	 * @var \Illuminate\Filesystem\Filesystem
	 */
	protected $files;

	/**
	 * The default configuration path.
	 *
	 * @var string
	 */
	protected $defaultPath;
	protected $modulePaths = [];

	/**
	 * Create a new file configuration loader.
	 *
	 * @param  \Illuminate\Filesystem\Filesystem  $files
	 * @param  string  $defaultPath
	 * @return void
	 */
	public function __construct(Filesystem $files, $defaultPath)
	{
		$this->files = $files;
		$this->defaultPath = $defaultPath;

		$this->getFolders();
	}

   /**
     * Load all required files from the modules at boot
     *
     * @return void
     */
    public function getFolders()
	{
		if ( ! $this->files->isDirectory($this->defaultPath))
		{
			return false;
		}

    	if (empty($this->modulePaths))
    	{
	    	$path = $this->defaultPath;
			

	        // Collect module dirs
	   		$modules = ! empty($path) ? $this->files->directories($path) : [];

	        //Build an associative array of modules [modulename => path]
	        foreach ($modules as $module)
	        {
	            //parse modules name
	            $name = strtolower(last(explode('/', str_replace('\\', '/', $module))));

	            //construct frieldy module path, we want only forward slashes
	            $path = str_replace('\\', '/', $module);

	            //add module to collection
	            $this->modulePaths[$name] = $path;
	        }
	    }
        return $this->modulePaths;
    }

	/**
	 * Determine if the given group exists.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function exists($name)
	{
		return isset($this->modulePaths[$name]);
	}

    /**
     * Get a specific module path
     *
     * @param  string $module;
     * @return string
     */
    public function getFile($module, $file='')
    {
        if (array_key_exists($module, $this->modulePaths))
        {
            return str_finish($this->modulePaths[$module], '/').$file;
        }

        return null;
    }

    /**
     * Get module path from the list
     * 
     * @param  string $name
     * @return string
     */
    public function getPath($name)
    {
    	return $this->modulePaths[$name];
    }

    /**
     * Check if a module's file exists
     * 
     * @param  string $name
     * @param  string $file
     * @return bool
     */
    public function fileExists($name, $file)
    {
    	return file_exists($this->getFile($name, $file));
    }


	/**
	 * Get a file's contents by requiring it.
	 *
	 * @param  string  $path
	 * @return mixed
	 */
	protected function getRequire($path)
	{
		return $this->files->getRequire($path);
	}

	/**
	 * Get the Filesystem instance.
	 *
	 * @return \Illuminate\Filesystem\Filesystem
	 */
	public function getFilesystem()
	{
		return $this->files;
	}

}
