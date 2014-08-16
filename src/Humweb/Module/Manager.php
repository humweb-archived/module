<?php namespace Humweb\Module;

use Illuminate\Support\Manager as AbstractManager;

class Manager extends AbstractManager {

	/**
	 * Create an instance of the array driver.
	 *
	 * @return \Humweb\Module\ArrayStore
	 */
	protected function createArrayDriver()
	{
		return $this->repository(new ArrayStore);
	}

	/**
	 * Create an instance of the file driver.
	 *
	 * @return \Humweb\Module\FileStore
	 */
	protected function createFileDriver()
	{
		$path = $this->app['config']['modules::storage_path'];

		return $this->repository(new FileStore($this->app['files'], $path));
	}

	/**
	 * Create an instance of the database driver.
	 *
	 * @return \Humweb\Module\DatabaseStore
	 */
	protected function createDatabaseDriver()
	{
		$connection = $this->app['config']['modules::db.connection'];

		$connection = $this->app['db']->connection($connection);

		$table = $this->app['config']['modules::db.table'];

		return $this->repository(new DatabaseStore($connection, $table));
	}


	/**
	 * Create a new module repository with the given implementation.
	 *
	 * @param  \Humweb\Module\StoreInterface  $store
	 * @return \Humweb\Module\Repository
	 */
	protected function repository(StoreInterface $store)
	{
		return new Repository($this->app['modules.container'], $this->app['modules.fileloader'], $store);
	}

	/**
	 * Get the default module driver name.
	 *
	 * @return string
	 */
	public function getDefaultDriver()
	{
		return $this->app['config']['modules::driver'];
	}

    /**
     * Set the default module driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['modules::driver'] = $name;
    }
}