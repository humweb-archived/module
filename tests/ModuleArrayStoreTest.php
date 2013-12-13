<?php

use Humweb\Module\ArrayStore;
use Humweb\Module\StoreInterface;

class ModuleArrayStoreTest extends PHPUnit_Framework_TestCase {

    // protected function getPackageProviders()
    // {
    //     return array('Humweb\Module\ModuleServiceProvider');
    // }

	public $dataset = [
		'foo' => [
			'slug' => 'bar',
			'status' => StoreInterface::STATUS_DISABLED
		],
		'pages' => [
			'slug' => 'pages',
			'status' => StoreInterface::STATUS_INSTALL
		],
		'menus' => [
			'slug' => 'menus',
			'status' => StoreInterface::STATUS_INSTALLED
		],
		'settings' => [
			'slug' => 'settings',
			'status' => StoreInterface::STATUS_INSTALLED
		],
		'blog' => [
			'slug' => 'blog',
			'status' => StoreInterface::STATUS_UPGRADE
		],
	];

	public function testItemsCanBeUpdatedAndRetrieved()
	{
		$store = new ArrayStore;

		$store->insert('foo', array('bar'=>'asdf'));

		$value = $store->find('foo');

		$this->assertEquals('asdf', $value['bar']);
	}

	public function testItemsCanBeSetAndRetrieved()
	{
		$store = new ArrayStore($this->dataset);

		$store->update('blog', array('bar'=>'asdf'));

		$value = $store->find('blog');

		$this->assertEquals('asdf', $value['bar']);
	}
	
	public function testItemsCanBeDeleted()
	{
		$store = new ArrayStore($this->dataset);

		$value = $store->find('blog');

		$this->assertEquals(true, isset($value['slug']));

		$store->delete('blog');

		$value = $store->find('blog');
		$this->assertEquals(false, isset($value['slug4']));

	}
	
	public function testGetEnabledItems()
	{
		$store = new ArrayStore($this->dataset);

		$results = $store->getEnabled();

		$this->assertEquals(2, count($results));

		$this->assertEquals('menus', $results['menus']['slug']);
	}

	public function testGetInstalledItems()
	{
		$store = new ArrayStore($this->dataset);

		$results = $store->getInstalled();

		$this->assertEquals(3, count($results));

		$this->assertEquals('menus', $results['menus']['slug']);
	}

	public function testGetUpgradableItems()
	{
		$store = new ArrayStore($this->dataset);

		$results = $store->getUpgradable();

		$this->assertEquals(1, count($results));

		$this->assertEquals('blog', $results['blog']['slug']);
	}

	public function testGetDisabledItems()
	{
		$store = new ArrayStore($this->dataset);

		$results = $store->getDisabled();

		$this->assertEquals(1, count($results));

		$this->assertEquals('bar', $results['foo']['slug']);
	}

}