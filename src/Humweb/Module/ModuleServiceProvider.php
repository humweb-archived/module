<?php namespace Humweb\Module;

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
        	$path = base_path().'/'.$app['config']['module::path'];
        	return new FileLoader($app['files'], $path);
        });
        
        $this->app->bindShared('modules', function ($app)
        {
           return new Provider($app,
						       $app['modules.container'],
						       $app['modules.manager'],
						       $app['modules.fileloader'],
						       $app['config']);
        });

		$this->registerSetupCommand();
		$this->registerCreatorCommand();
    }
	

	public function registerSetupCommand()
	{
        $this->app->bindShared('modules.command.setup', function($app) 
        {
            return new Console\SetupCommand($app['files']);
        });

		$this->commands('modules.command.setup');
	}
	
	public function registerCreatorCommand()
	{
		$this->app->bindShared('modules.creator', function($app)
		{
			return new ModuleCreator($app['files']);
		});

		$this->app->bindShared('modules.generator.command', function($app)
		{
			return new Console\ModuleGeneratorCommand($app['modules.creator']);
		});

		$this->commands('modules.generator.command');
	}
	
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('humweb/module');
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