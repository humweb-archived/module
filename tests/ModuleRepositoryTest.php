<?php

use Mockery as m;
use Ryun\Module\ArrayStore;
use Ryun\Module\Repository;

class ModuleRepositoryTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}
	
	public function testGetReturnsValue()
	{
		$repo = $this->getRepository();
		$repo->getStore()->shouldReceive('find')->once()->with('foo')->andReturn('bar');
		$this->assertEquals('bar', $repo->find('foo'));
	}
	
	public function testSetValueAndReturnValue()
	{
		$attr = ['bar'=>'baz'];
		$repo = $this->getRepository();
		$repo->getStore()->shouldReceive('insert')->once()->with('foo', $attr);
		$repo->getStore()->shouldReceive('find')->once()->with('foo')->andReturn($attr);
		$result = $repo->insert('foo', $attr);
		$result = $repo->find('foo');
		$this->assertEquals($attr, $result);
	}


	protected function getRepository()
	{
		return new Repository(m::mock('Ryun\Module\ProviderInterface'),m::mock('Ryun\Module\LoaderInterface'),m::mock('Ryun\Module\StoreInterface'));
	}

}