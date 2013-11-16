<?php namespace Ryun\Module;

use Illuminate\Support\Manager as AbstractManager;

class Manager extends AbstractManager {

	
	/**
	 * Create an instance of the array cache driver.
	 *
	 * @return \Ryun\Module\ArrayStore
	 */
	protected function createArrayDriver()
	{
		return $this->repository(new ArrayStore);
	}

	/**
	 * Create an instance of the file cache driver.
	 *
	 * @return \Ryun\Module\FileStore
	 */
	protected function createFileDriver()
	{
		$path = $this->app['config']['module::storage.path'];

		return $this->repository(new FileStore($this->app['files'], $path));
	}

	/**
	 * Create an instance of the database cache driver.
	 *
	 * @return \Ryun\Module\DatabaseStore
	 */
	protected function createDatabaseDriver()
	{
		$connection = $this->app['config']['module::db.connection'];

		$connection = $this->app['db']->connection($connection);

		// We allow the developer to specify which connection and table should be used
		// to store the cached items. We also need to grab a prefix in case a table
		// is being used by multiple applications although this is very unlikely.
		$table = $this->app['config']['module::db.table'];

		return $this->repository(new DatabaseStore($connection, $table));
	}


	/**
	 * Create a new cache repository with the given implementation.
	 *
	 * @param  \Ryun\Module\StoreInterface  $store
	 * @return \Ryun\Module\Repository
	 */
	protected function repository(StoreInterface $store)
	{
		return new Repository($this->app['module'], $this->app['modules.fileloader'], $store);
	}

	/**
	 * Get the default cache driver name.
	 *
	 * @return string
	 */
	protected function getDefaultDriver()
	{
		return $this->app['config']['modules::driver'];
	}

}