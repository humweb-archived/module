<?php

use Mockery as m;
use Ryun\Module\ArrayStore;
use Ryun\Module\Repository;

class CacheRepositoryTest extends PHPUnit_Framework_TestCase {

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
		$repo = $this->getRepository();
		$repo->getStore()->shouldReceive('insert')->once()->with('foo', ['bar'=>'baz']);
		$repo->getStore()->shouldReceive('find')->once()->with('foo')->andReturn(['bar'=>'baz']);
		$result = $repo->insert('foo', ['bar'=>'baz']);
		$result = $repo->find('foo');
		$this->assertEquals(['bar'=>'baz'], $result);
	}


	protected function getRepository()
	{
		return new Repository(m::mock('Ryun\Module\ProviderInterface'),m::mock('Ryun\Module\LoaderInterface'),m::mock('Ryun\Module\StoreInterface'));
	}

}