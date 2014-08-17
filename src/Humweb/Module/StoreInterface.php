<?php namespace Humweb\Module;

/**
 * Module StoreInterface
 * 
 */
interface StoreInterface
{
    const STATUS_DISABLED  = -1;
    const STATUS_INSTALLABLE   = 0;
    const STATUS_INSTALLED = 1;
    const STATUS_ENABLED = 2;
    const STATUS_UPGRADABLE   = 3;
    
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
    public function insert($slug, array $attributes = []);

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
     * Fetch all modules
     * 
     * @return array
     */
    public function getAll();
    
    /**
     * Fetch only modules that are disabled
     * 
     * @return array
     */
    public function getDisabled();

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
