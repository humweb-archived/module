<?php namespace Ryun\Module;

use App, Config;
use Illuminate\Filesystem\Filesystem;
use Basic\Core\Models\ModuleModel;

/**
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

    public $module_paths = [];
    public $module_providers = [];


    /**
     * Create a new manager instance.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app       = $app;
        $this->container = $this->app['modules.container'];
        $this->manager   = $this->app['modules.manager'];
        $this->loader    = $this->app['modules.fileloader'];

        //@todo use ConfigInterface
        $this->path      = $this->app['config']['module::path'];
        $this->namespace = $this->app['config']['module::namespace'];
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
        $modules = $this->loader->getFolders();

        //Build an associative array of modules [modulename => path]
        foreach ($modules as $name => $path)
        {
            if ($this->validateModule($name))
            {
                $this->bootModule($name);
                $this->addNamespace($name);
            }
        }
    }

    /**
     * This uses our abstract container
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

            //Bind module to container for later use
            $this->container->bind($module, function() use ($className)
            {
                return new $className();
            });
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
            //$asset_path = $vendor ? $vendor.'/'.$name : $name;
            //App::make('asset')->addNamespace($name, $asset_path.'/asset');
    }

}
