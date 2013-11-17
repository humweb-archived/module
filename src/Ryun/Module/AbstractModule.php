<?php namespace Ryun\Module;

abstract class AbstractModule
{
    protected $app;
    public $name          = 'n/a';
    public $version       = 'n/a';
    public $author        = 'n/a';
    public $website       = 'n/a';
    public $license       = 'n/a';
    public $description   = 'n/a';
    public $admin_section = 'Content';
    public $autoload      = [];

    public function __construct($app)
    {
        $this->app = $app;
    }
    public function boot($app) {return true;}

    public function install() {return true;}
    public function upgrade() {return true;}

    public function admin_menu()
    {
        return [];
    }

    public function admin_quick_menu()
    {
        return [];
    }

    /**
     * Register the package's custom Artisan commands.
     *
     * @param  array  $commands
     * @return void
     */
    public function commands($commands)
    {
        $commands = is_array($commands) ? $commands : func_get_args();

        // To register the commands with Artisan, we will grab each of the arguments
        // passed into the method and listen for Artisan "start" event which will
        // give us the Artisan console instance which we will give commands to.
        $events = $this->app['events'];

        $events->listen('artisan.start', function($artisan) use ($commands)
        {
            $artisan->resolveCommands($commands);
        });
    }
}
