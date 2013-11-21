<?php namespace Humweb\Module;

use Illuminate\Database\Connection;

class DatabaseStore implements StoreInterface {

	/**
	 * The database connection instance.
	 *
	 * @var \Illuminate\Database\Connection
	 */
	protected $connection;

	/**
	 * The name of the storage table.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * Create a new database store.
	 *
	 * @param  \Illuminate\Database\Connection  $connection
	 * @param  string  $table
	 * @return void
	 */
	public function __construct(Connection $connection, $table)
	{
		$this->table = $table;
		$this->connection = $connection;
	}
	    /**
     * Define the table name
     *
     * @var string
     */
    protected $table = 'modules';
	
    public function find($slug)
    {
      return $this->table()->where('slug', $slug)->first();
    }
    
    public function getAll()
    {
      return $this->table()->all();
    }
    
    public function getEnabled()
    {
        return $this->getBy('status', ProviderInterface::STATUS_INSTALLED);
    }
    
    public function getDisabled()
    {
        return $this->getBy('status', ProviderInterface::STATUS_DISABLED);
    }

    public function getInstalled()
    {
        return $this->getBy('status', ProviderInterface::STATUS_DISABLED, '!=');
    }

    public function getUpgradable()
    {
        return $this->getBy('status', ProviderInterface::STATUS_UPGRADE);
    }

    public function insert($slug, $attributes = [])
    {
        return $this->table()->insertGetId($attributes);
    }

    public function update($slug, $attributes = [])
    {
        return $this->table()->where('slug', $slug)->update($attributes);
    }

    public function delete($slug)
    {
        return $this->table()->where('slug', $slug)->delete();
    }

    protected function getBy($field, $value, $op = '=')
    {
        return $this->table()->where($field, $op, $value)->get()->toArray();
    }

	/**
	 * Get a query builder for the module table.
	 *
	 * @return \Illuminate\Database\Query\Builder
	 */
	protected function table()
	{
		return $this->connection->table($this->table);
	}

	/**
	 * Get the underlying database connection.
	 *
	 * @return \Illuminate\Database\Connection
	 */
	public function getConnection()
	{
		return $this->connection;
	}

}
