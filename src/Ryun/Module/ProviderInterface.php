<?php namespace Ryun\Module;

use App, Config;
use Illuminate\Filesystem\Filesystem;

/**
 * @todo  load module permissions
 */
interface ProviderInterface
{
    const STATUS_DISABLED  = -1;
    const STATUS_INSTALL   = 0;
    const STATUS_INSTALLED = 1;
    const STATUS_UPGRADE   = 2;

    public function getPath();
    public function getNamespace();
    public function getContainer();
    public function getLoader();
    public function boot();
    public function instance($module, $class = 'Module');
    public function validateModule($module);
    public function bootModule($module);
    public function addNamespace($name);

}
