<?php namespace Ryun\Module;

/**
 * Module StoreInterface
 * 
 */
interface StoreInterface
{
    const STATUS_DISABLED  = -1;
    const STATUS_INSTALL   = 0;
    const STATUS_INSTALLED = 1;
    const STATUS_UPGRADE   = 2;
    
    /**
     * Update a specific module record
     * 
     * @param  string $slug
     * @param  array $attributes
     * @return bool
     */
    public function update($slug, array $attributes = []);

    /**
     * Insert module record
     * 
     * @param  array $attributes
     * @return bool
     */
    public function insert(array $attributes = []);

    /**
     * Delete module record
     * 
     * @param  string $slug
     * @return bool
     */
    public function delete($slug);

    /**
     * Find a single module record by slug id
     * 
     * @param  string $slug
     * @return array
     */
    public function find($slug);
    
    /**
     * Fetch only modules that are enabled
     * 
     * @return array
     */
    public function getEnabled();

    /**
     * Fetch only modules that are installed
     * 
     * @return array
     */
    public function getInstalled();

    /**
     * Fetch only modules that are upgradable
     * 
     * @return array
     */
    public function getUpgradable();
}
