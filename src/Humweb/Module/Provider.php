<?php namespace Humweb\Module;

/**
 * Module Provider class
 * 
 * @todo  load module permissions
 */
class Provider implements ProviderInterface
{

    /**
     * The application instance.
     *
     * @var Illuminate\Foundation\Application
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
     * 
     * @var string
     */
    public $namespace;

    /**
     * Module container
     * 
     * @var Humweb\Module\Container
     */
    protected $container;

    /**
     * Manager
     * 
     * @var Humweb\Module\Manager
     */
    protected $manager;

    /**
     * Fileloader
     * 
     * @var Humweb\Module\Fileloader
     */
    protected $loader;

    /**
     * Config instance
     * 
     * @var Illuminate\Config\Repository
     */
    protected $config;


    /**
     * Create a new provider instance.
     *
     * @param  
     * @return void
     */
    
    /**
     * Creates a new Provider instance
     * @param Illuminate\Foundation\Application $app
     * @param Container  $container
     * @param Manager    $manager
     * @param Fileloader $loader
     * @param Illuminate\Config\Repository $config
     */
    public function __construct($app = null, Container $container, Manager $manager, FileloaderInterface $loader, $config)
    {
        $this->app       = $app ?: new \Illuminate\Foundation\Application;
        $this->container = $container;
        $this->manager   = $manager;
        $this->loader    = $loader;
        $this->config    = $config;

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
     * @return bool|void
     */
    public function boot()
    {
        // Maybe? collect module dirs
        if ( ! ($modules = $this->loader->getFolders()))
        {

            // If we are not running in console, and we can't find any folders,
            // stop the show right here!
            if ( ! $this->app->runningInConsole())
            {
                throw new \Exception("Modules folder not found, may need to run setup.", 1);
            }

            // If we are not in a console this allows `module:setup` command
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
     * @param  string $module
     * @param  string $class
     * @return AbstractModule
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

            //Fire event before we bind module
            $this->app['events']->fire('before.bind: '.$module, array($this->app, $module, $className));

            //Bind module provider to container for later use
            $this->container->bind($module, function($app) use ($className)
            {
                return new $className($app);
            });

            //Fire an event after we bind module
            $this->app['events']->fire('after.bind: '.$module, array($this->app, $module, $className));
        }

        return $this->container->instance($module);
    }

    /**
     * Checks if modules service provider is available
     *
     * @param  string $module
     * @return bool
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
            $this->app['config']->addNamespace($name, $path.'/Config');
            $this->app['translator']->addNamespace($name, $path.'/Lang');
    }

}