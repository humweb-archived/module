<?php namespace Ryun\Module;

/**
 * Module Provider class
 * @todo  load module permissions
 */
class Provider implements ProviderInterface
{

    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Modules base path
     *
     * @var string
     */
    public $path;

    /** 
     * Modules root namespace
     * @var [type]
     */
    public $namespace;

    protected $container;
    protected $manager;
    protected $loader;
    protected $config;


    /**
     * Create a new provider instance.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct($app, $container, $manager, $loader, $config)
    {
        $this->app       = $app;
        $this->container = $container;
        $this->manager   = $manager;
        $this->loader    = $loader;
        $this->config    = $config;

        //@todo use ConfigInterface
        $this->path      = $this->config['module::path'];
        $this->namespace = $this->config['module::namespace'];
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }
    public function getContainer()
    {
        return $this->container;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Load all required files from the modules at boot
     *
     * @return void
     */
    public function boot()
    {
        // Collect module dirs
        if ( ! ($modules = $this->loader->getFolders()))
        {
            if ( ! $this->app->runningInConsole())
            {
                throw new \Exception("Modules folder not found, may need to run setup.", 1);
            }
            return false;
        }

        //Build an associative array of modules [modulename => path]
        foreach ($modules as $name => $path)
        {
            if ($this->validateModule($name))
            {
                $this->app['events']->fire('before.boot: '.$name, array($this->app, $name));
                $this->bootModule($name);
                $this->addNamespace($name);
                $this->app['events']->fire('after.boot: '.$name, array($this->app, $name));
            }
        }
    }

    /**
     * This uses our abstract container
     * 
     * @param  [type] $module [description]
     * @param  string $class  [description]
     * @return [type]         [description]
     */
    public function instance($module, $class = 'Module')
    {
        //Check is module is bound to the container
        //This is to make sure we dont instantiate a module twice.
        if ( ! $this->container->bound($module))
        {
            //Build fully namespaced class name
            $className = $this->namespace.'\\'.ucfirst($module).'\\'.$class;

            if ( ! class_exists($className))
            {
                throw new \Exception("Error Module Class Not Found", 1);
            }
            // $app = $this->app;
            //Bind module to container for later use
            $this->app['events']->fire('before.bind: '.$module, array($this->app, $module, $className));

            $this->container->bind($module, function($app) use ($className)
            {
                return new $className($app);
            });

            $this->app['events']->fire('after.bind: '.$module, array($this->app, $module, $className));
        }

        return $this->container->instance($module);
    }

    /**
     * Checks if modules service provider is available
     * Logs error
     *
     * @return void
     */
    public function validateModule($module)
    {
        if ( ! $this->loader->fileExists($module,'Module.php'))
        {
            \Log::info('Missing required file "modules.php" for module: ' . $module);

            return false;
        }

        return true;
    }

    /**
     * Load modules files configured from the autoload array
     *
     * @param  string $module
     * @return void
     */
    public function bootModule($module)
    {
        $instance = $this->instance($module);

        //Bootsrap
        //@todo maybe check if booted
        if ( method_exists($instance, 'boot'))
        {
            $instance->boot($this->app);
        }

        //Load autoload files
        if (isset($instance->autoload) and ! empty($instance->autoload))
        {
            foreach ($instance->autoload as $file)
            {
                //Get file path for module
                $file = $this->loader->getFile($module, $file);

                //Make sure it exists
                if (file_exists($file))
                {
                    include $file;
                }
            }
        }
    }

    /**
     * Add namespaces and paths for modules
     *
     * @return void
     */
    public function addNamespace($name)
    {
            $name = strtolower($name);
            $path =  $this->loader->getPath($name);

            $this->app['view']->addNamespace($name, $path.'/Views');
            $this->app['config']->addNamespace($name, $path.'/config');
            $this->app['translator']->addNamespace($name, $path.'/lang');
    }

}
