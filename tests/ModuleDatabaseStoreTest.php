<?php

    use Humweb\Module\ArrayStore;
    use Humweb\Module\StoreInterface;

    use Illuminate\Foundation\Testing\ApplicationTrait;
    use Illuminate\Foundation\Testing\AssertionsTrait;
    use Illuminate\Support\Facades\Artisan;

    class ModuleArrayStoreTest extends PHPUnit_Framework_TestCase {
        use ApplicationTrait, AssertionsTrait;

        protected $dataset = [
        'foo' => [
        'name' => 'foo',
        'slug' => 'bar',
        'status' => StoreInterface::STATUS_DISABLED
        ],
        'pages' => [
        'slug' => 'pages',
        'status' => StoreInterface::STATUS_UPGRADABLE
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
        'status' => StoreInterface::STATUS_UPGRADABLE
        ],
    ];

    public function setUp()
    {
        parent::setUp();

        if ( ! $this->app)
        {
            $this->refreshApplication();
        }

        $this->db = new DatabaseStore(Connection $connection, $table);
        $this->app['config']['modules::db.connection'];

		$connection = $this->app['db']->connection($connection);

        Artisan::call('migrate', array('--package' => 'humweb/module'));
        //Artisan::call('db:seed', array('--class' => 'UserTableSeeder'));

        foreach($this->dataset as $record)
        {

        }

    }

	public $dataset = [
		[
			'name' => 'Bar',
			'slug' => 'Bar',
			'status' => StoreInterface::STATUS_DISABLED
		],
		[
			'name' => 'Pages',
			'slug' => 'Pages',
			'status' => StoreInterface::STATUS_UPGRADABLE
		],
		[
			'name' => 'Menus',
			'slug' => 'Menus',
			'status' => StoreInterface::STATUS_INSTALLED
		],
		[
			'name' => 'Settings',
			'slug' => 'Settings',
			'status' => StoreInterface::STATUS_INSTALLED
		],
		[
			'name' => 'Blog',
			'slug' => 'Blog',
			'status' => StoreInterface::STATUS_UPGRADABLE
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

		$this->assertEquals(4, count($results));

		$this->assertEquals('menus', $results['menus']['slug']);
	}

	public function testGetUpgradableItems()
	{
		$store = new ArrayStore($this->dataset);

		$results = $store->getUpgradable();

		$this->assertEquals(2, count($results));

		$this->assertEquals('blog', $results['blog']['slug']);
	}

	public function testGetDisabledItems()
	{
		$store = new ArrayStore($this->dataset);

		$results = $store->getDisabled();

		$this->assertEquals(1, count($results));

		$this->assertEquals('bar', $results['foo']['slug']);
	}

        /**
         * Creates the application.
         *
         * @return \Symfony\Component\HttpKernel\HttpKernelInterface
         */
        public function createApplication()
        {
            $unitTesting = true;

            $testEnvironment = 'testing';

            return require __DIR__.'/../../bootstrap/start.php';
        }

    }