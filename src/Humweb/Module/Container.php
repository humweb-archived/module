<?php namespace Humweb\Module;

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
    protected $providers = [];
    protected $items = [];

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * This uses our abstract container
     *
     * @param $name
     * @return AbstractModule
     */
    public function instance($name)
    {
        $name = $this->prefixName($name);

        return $this->app->bound($name) ? $this->app[$name] : null;
    }

    public function all()
    {
        if (empty($this->providers))
        {
            foreach ($this->items as $key => $value)
            {
                $this->providers[$key] = $this->instance($key);
            }
        }
        return $this->providers;
    }

    public function bind($name, Closure $binding)
    {
        $this->items[$name] = true;
        $name = $this->prefixName($name);
        $this->app[$name] = $this->app->share($binding);
    }

    public function bindByModuleName($module, $paths = [], $namespace = null)
    {
        if ( ! $namespace)
        {
            $namespace = $this->app['config']['modules::namespace'];
        }
        //Check is module is bound to the container
        //This is to make sure we dont instantiate a module twice.
        if ( ! $this->bound($module))
        {

            //Build fully namespaced class name
            $className = $namespace.'\\'.ucfirst($module).'\\Module';

            if (!class_exists($className))
            {
                throw new \Exception("Unable to locate module class: ".$className, 1);
            }

            //Fire event before we bind module
            $this->app['events']->fire('modules.binding:'.$module, array($this->app, $module, $className));

            //Bind module provider to container for later use
            $this->bind($module, function ($app) use ($className, $paths)
            {
                return new $className($app, $paths);
            });

            //Fire an event after we bind module
            $this->app['events']->fire('modules.bound:'.$module, array($this->app, $module, $className));
        }
        return $this->instance($module);
    }

    public function unbind($name)
    {
        unset($this->items[$name]);
        $name = $this->prefixName($name);
        unset($this->app[$name]);
    }
    
    public function bound($name)
    {
        $name = $this->prefixName($name);

        return $this->app->bound($name);
    }

    // /**
    //  * Dynamically handle calls to the html class.
    //  *
    //  * @param  string  $method
    //  * @param  array   $parameters
    //  * @return mixed
    //  */
    // public function __call($method, $parameters)
    // {
    //     $method = $this->app->$method;

    //     if (is_callable($method))
    //     {
    //         return call_user_func_array($method, $parameters);
    //     }

    //     throw new \BadMethodCallException("Method {$method} does not exist.");
    // }

    public function prefixName($name)
    {
        return $this->app['config']['modules::container_prefix'].$name;
    }
}
