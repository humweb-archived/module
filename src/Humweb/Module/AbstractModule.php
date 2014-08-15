<?php namespace Humweb\Module;

abstract class AbstractModule
{
    /**
     * The application instance.
     *
     * @var Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Module name
     * @var string
     */
    public $name          = 'n/a';

    /**
     * Module version
     * @var string
     */
    public $version       = 'n/a';

    /**
     * Module author
     * @var string
     */
    public $author        = 'n/a';

    /**
     * Module website
     *
     * @var string
     */
    public $website       = 'n/a';

    /**
     * Module license
     *
     * @var string
     */
    public $license       = 'n/a';

    /**
     * Module description
     *
     * @var string
     */
    public $description   = 'n/a';

    /**
     * Admin section to place the modules menu items under
     *
     * @var string
     */
    public $adminCategory = 'Content';

    /**
     * Autoload files
     *
     * @var array
     */
    public $autoload      = [];

    /**
     * Paths associated with the module
     *
     * @var array
     */
    public $paths         = [];


    /**
     * Create service provider instance
     *
     * @param Illuminate\Foundation\Application $app
     * @param array                             $paths
     */
    public function __construct($app, $paths = [])
    {
        $this->app = $app;
        $this->paths = $paths;
    }

    /**
     * Runs when module is booted
     * 
     * @return mixed
     */
    public function boot()
    {

    }

    /**
     * Install logic for module
     * 
     * @return bool
     */
    public function install()
    {
        return true;
    }

    /**
     * Upgrade logic for module
     * 
     * @return bool
     */
    public function upgrade()
    {
        return true;
    }

    /**
     * Used modify admin menu
     * 
     * @return array
     */
    public function adminMenu()
    {
        return [];
    }

    /**
     * Sub/Quick menu items
     * 
     * @return array
     */
    public function admin_quick_menu()
    {
        return [];
    }

    /**
     * Register the modules custom Artisan commands.
     *
     * @param  array  $commands
     * @return void
     */
    public function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        $this->app['events']->listen('artisan.start', function($artisan) use ($commands)
        {
            $artisan->resolveCommands($commands);
        });
    }

    protected function onEvent($name, $handler, $priority = null)
    {
        return $this->app['events']->listen($name, $handler, $priority);
    }
    protected function fireEvent($name, $params = [])
    {
        return $this->app['events']->fire($name, $params);
    }
}
