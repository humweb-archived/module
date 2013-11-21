<?php namespace Teststubs\Modules\Example;

use Ryun\Module\AbstractModule;

class Module extends AbstractModule {
	public $name        = 'Example';
	public $description = 'Example module';
	public $version     = '1.0';
	public $author      = 'Ryun Shofner';
	public $website     = 'humboldtweb.com';
	public $license     = 'BSD-3-Clause';
	public $autoload    = ["routes.php"];
	
	/**
	 * Use this method to register classes with the IoC container
	 */
	public function boot()
	{
		//code to run at boot time
	}
	
	public function install(){return true;}
	public function upgrade(){return true;}
}