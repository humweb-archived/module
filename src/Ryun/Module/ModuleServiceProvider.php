<?php namespace Ryun\Module;

use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    public function register()
    {

        $this->app->bindShared('modules.container', function ($app)
        {
        	return new Container($app);
        });

		$this->app->bindShared('modules.manager', function ($app)
		{
			return new Manager($app);
		});

        $this->app->bindShared('modules.fileloader', function ($app)
        {
        	return new FileLoader($app['files'], $app['config']['module::path']);
        });
        
        $this->app->bindShared('modules', function ($app)
        {
           return new Provider($app,
						       $app['modules.container'],
						       $app['modules.manager'],
						       $app['modules.fileloader'],
						       $app['config']);
        });

        $this->app->bindShared('modules.command.setup', function($app) 
        {
                return new Console\SetupCommand($app['files']);
        });

		$this->commands('modules.command.setup');
    }
	
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('ryun/module');
		$this->app['modules']->boot();
	}
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('module', 'modules system', 'module manager');
	}

}