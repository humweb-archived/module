<?php

use Humweb\Module\Container;
use Illuminate\Container\Container as AppContainer;

class ModuleContainerContainerTest extends PHPUnit_Framework_TestCase {

    // protected function getPackageProviders()
    // {
    //     return array('Humweb\Module\ModuleServiceProvider');
    // }

	public function testClosureResolution()
	{
		$container = $this->getContainer();
		$container->bind('name', function() { return 'Acme'; });
		$this->assertEquals('Acme', $container->instance('name'));
	}

	public function testUnbindBoundInstance()
	{
		$container = $this->getContainer();
		$container->bind('object', function() { return 'Acme'; });
		
		$this->assertTrue($container->bound('object'));
		
		$container->unbind('object');

		$this->assertFalse($container->bound('object'));
	}

	private function getContainer()
	{
		$app = new AppContainer;
		$app['config'] = function() { return ['module::container_prefix' => 'test']; };
		return new Container($app);
	}
}