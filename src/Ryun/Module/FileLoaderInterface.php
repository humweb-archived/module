<?php namespace Ryun\Module;

interface FileLoaderInterface {

   /**
     * Load all required files from the modules at boot
     *
     * @return array
     */
    public function getFolders();


	/**
	 * Determine if the given group exists.
	 *
	 * @param  string  $name
	 * @return bool
	 */
	public function exists($name);


    /**
     * Get a specific module path
     *
     * @param  string $module;
     * @return string
     */
    public function getFile($module, $file='');


    /**
     * Get module path from the list
     * 
     * @param  string $name
     * @return string
     */
    public function getPath($name);


    /**
     * Check if a module's file exists
     * 
     * @param  string $name
     * @param  string $file
     * @return bool
     */
    public function fileExists($name, $file);


	/**
	 * Get a file's contents by requiring it.
	 *
	 * @param  string  $path
	 * @return mixed
	 */
	public function getRequire($path);


	/**
	 * Get the Filesystem instance.
	 *
	 * @return \Illuminate\Filesystem\Filesystem
	 */
	public function getFilesystem();

}
