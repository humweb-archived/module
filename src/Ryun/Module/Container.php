<?php namespace Ryun\Module;

use Closure;


/**
* Module Container
* 
* @note
* Basicly a slim wrapper around the Illuminate IoC container
* It uses a prefix which can be changed in the config file
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
    
    public function unbind($name)
    {
        $name = $this->prefixName($name);

        unset($this->app[$name]);
    }
    
    public function bound($name)
    {
        $name = $this->prefixName($name);

        return $this->app->bound($name);
    }

    /**
     * Dynamically handle calls to the html class.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $method = $this->app->$method;

        if (is_callable($method))
        {
            return call_user_func_array($method, $parameters);
        }

        throw new \BadMethodCallException("Method {$method} does not exist.");
    }

    public function prefixName($name)
    {
        return $this->app['config']['module::container_prefix'].$name;
    }
}
