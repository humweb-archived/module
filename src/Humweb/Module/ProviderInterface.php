<?php namespace Humweb\Module;

interface ProviderInterface
{
    const STATUS_DISABLED  = -1;
    const STATUS_INSTALLABLE   = 0;
    const STATUS_INSTALLED = 1;
    const STATUS_ENABLED = 2;
    const STATUS_UPGRADABLE   = 3;

    public function getPath();
    public function getNamespace();
    public function getContainer();
    public function getLoader();
    public function boot();
    public function bindInstance($module, $moduleSuffix = 'Module');
    public function validateModule($module);
    public function bootModule($module);
    public function addNamespace($name);

}
