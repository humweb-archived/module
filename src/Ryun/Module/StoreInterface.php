<?php namespace Ryun\Module;

use Basic\Core\Facades\Modules,
    Log;

/**
 * Module Interface
 */
interface StoreInterface
{
    const STATUS_DISABLED  = -1;
    const STATUS_INSTALL   = 0;
    const STATUS_INSTALLED = 1;
    const STATUS_UPGRADE   = 2;
    
    public function update($slug, array $attributes = []);
    public function insert(array $attributes = []);
    public function delete($slug);
    public function find($slug);
    
    public function getEnabled();
    public function getInstalled();
    public function getUpgradable();
}
