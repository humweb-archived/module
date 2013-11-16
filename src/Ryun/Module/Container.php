<?php namespace Ryun\Module;

use Closure;

/**
* Module Container
* 
* @note
* Basicly a slim wrapper around the Illuminate IoC container
* It also uses a prefix which can be changed in the config file
*/
class Container
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function instance($name)
    {

        $name = $this->prefixName($name);

        return $this->app->bound($name) ? $this->app[$name] : null;
    }

    public function bind($name, Closure $binding)
    {
        $name = $this->prefixName($name);

        $this->app[$name] = $this->app->share($binding);
    }

    public function bound($name)
    {
        $name = $this->prefixName($name);

        return $this->app->bound($name);
    }

    public function prefixName($name)
    {
        return $this->app['config']['module::container_prefix'].$name;
    }
}
