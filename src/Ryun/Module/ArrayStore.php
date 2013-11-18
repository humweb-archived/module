<?php namespace Ryun\Module;

/**
 * Module StoreInterface
 * 
 */
class ArrayStore implements StoreInterface
{

    /**
     * The array of stored values.
     *
     * @var array
     */
    protected $storage = array();

    public function __construct(array $data = [])
    {
        $this->storage = $data;
    }

    /**
     * Update a specific module record
     * 
     * @param  string $slug
     * @param  array $attributes
     * @return bool
     */
    public function update($slug, array $attributes = [])
    {
        return $this->storage[$slug] = $attributes;
    }

    /**
     * Insert module record
     * 
     * @param  array $attributes
     * @return bool
     */
    public function insert(array $attributes = [])
    {
        return $this->update($slug, $attributes);
    }

    /**
     * Delete module record
     * 
     * @param  string $slug
     * @return bool
     */
    public function delete($slug)
    {
        return unset($this->storage[$slug])
    }

    /**
     * Find a single module record by slug id
     * 
     * @param  string $slug
     * @return array
     */
    public function find($slug)
    {
        if (isset($this->storage[$slug]))
        {
            return $this->storage[$slug];
        }
    }
    
    /**
     * Fetch only modules that are enabled
     * 
     * @return array
     */
    public function getEnabled()
    {
        return array_filter($this->storage, function($val)
        {
            return $val['status'] === ProviderInterface::STATUS_INSTALLED;
        });
    }

    /**
     * Fetch only modules that are installed
     * 
     * @return array
     */
    public function getInstalled()
    {
        return array_filter($this->storage, function($val)
        {
            return $val['status'] ==! ProviderInterface::STATUS_DISABLED;
        });
    }

    /**
     * Fetch only modules that are upgradable
     * 
     * @return array
     */
    public function getUpgradable()
    {
        return array_filter($this->storage, function($val)
        {
            return $val['status'] === ProviderInterface::STATUS_UPGRADE;
        });
    }
}
