<?php namespace Humweb\Module;
 
use Carbon\Carbon;

/**
 * Module Repository class
 */
class Repository
{
    protected $model;
    protected $fileloader;

    /**
     * Create a new repository instance
     *
     * @param Container           $container
     * @param FileLoaderInterface $fileloader
     * @param StoreInterface      $model
     *
     * @internal param Humweb\Module\Provider $provider
     */
    public function __construct(Container $container = null, FileLoaderInterface $fileloader = null, StoreInterface $model = null)
    {
        $this->fileloader = $fileloader;
        $this->container = $container;
        $this->model = $model;
    }

    public function getStore()
    {
        return $this->model;
    }
    
    /**
     * Insert a single entry
     * 
     * @param  string $slug
     * @return array
     */
    public function insert($slug, $attr=[])
    {
        return $this->model->insert($slug, $attr);
    }

    /**
     * Find a single entry
     * 
     * @param  string $slug
     * @return array
     */
    public function find($slug)
    {
        return $this->model->find($slug);
    }
 
    /**
     * Fetch all available modules
     * 
     * @return array
     */
    public function getAll()
    {
        return $this->model->getAll();
    }

    /**
     * Fetch only modules that are installed
     *
     * @return array
     */
    public function getInstalled()
    {
        return $this->model->getInstalled();
    }

    /**
     * Fetch only modules that are enabled
     * 
     * @return array
     */
    public function getEnabled()
    {
        return $this->model->getEnabled();
    }


    //-------------------------------------------------------------
    // Update Module Status
    //-------------------------------------------------------------

    /**
     * Enable module
     * 
     * @param  string $slug
     * @return bool
     */
    public function enable($slug)
    {
        return $this->setStatus($slug, ProviderInterface::STATUS_ENABLED);
    }

    /**
     * Disable module
     * 
     * @param  string $slug
     * @return bool
     */
    public function disable($slug)
    {
        return $this->setStatus($slug, ProviderInterface::STATUS_DISABLED);
    }

    /**
     * Install module
     * 
     * @param  string $slug
     * @return bool
     */
    public function install($slug)
    {
        return $this->setStatus($slug, ProviderInterface::STATUS_INSTALLED);
    }

    /**
     * Helper method to update the status
     *
     * @param string $slug
     * @param int    $status
     *
     * @return bool
     */
    protected function setStatus($slug, $status)
    {
        return $this->model->update($slug, ['status' => $status]);
    }

    //-------------------------------------------------------------
    // Import new modules to data store
    //-------------------------------------------------------------

    public function importUnknown($namespace = '')
    {
        $modules = array();
        $return = array();

        $is_core = true;

        //$known = static::get();
        $knownModules = $this->model->getAll();
        $knownKeys = [];
        $installedKeys = [];
        $installable = [];

        // Format array to be more friendly
        if (count($knownModules) > 0)
        {
            foreach ($knownModules as $module)
            {
                $modules[$module->slug] = $module;
                $knownKeys[] = $module->slug;
            }
        }

        //Available Modules
        foreach ($this->fileloader->getFolders() as $path)
        {
            $slug = last(explode('/', str_replace('\\', '/', $path)));

            if (isset($modules[$slug]) and $modules[$slug]->status == ProviderInterface::STATUS_DISABLED)
            {
                continue;
            }

            if ($meta = $this->container->bindByModuleName($slug))
            {

                // These modules are known
                // So we check for upgrades
                if (in_array($slug, $knownKeys))
                {


                    $hasNewerVersion = version_compare($modules[$slug]->version, $meta->version, '<');

                    if ($modules[$slug]->status == ProviderInterface::STATUS_INSTALLABLE)
                    {

                        //$installable[$modules[$slug]->slug] = $modules[$slug];

                        if ($hasNewerVersion)
                        {
                            $this->model->update($slug, ['version' => $meta->version]);
                        }

                    }

                    //Compare versions and update if needed
                    elseif ($hasNewerVersion)
                    {
                        $moduleAttributes = [
                            'name'        => $meta->name,
                            'description' => $meta->description,
                            'status'      => ProviderInterface::STATUS_UPGRADE,
                            'updated_on'  => Carbon::now(),
                        ];

                        $this->model->update($slug, $moduleAttributes);
                        \Log::info(sprintf('The information of the module "%s" has been updated', $slug));
                    }
                }

                //Add new module to db
                else
                {

                    $moduleAttributes = [
                        'name'        => $meta->name,
                        'slug'        => $slug,
                        'version'     => $meta->version,
                        'description' => $meta->description,
                        'status'      => ProviderInterface::STATUS_INSTALLABLE,
                        'updated_on'  => Carbon::now(),
                    ];
                    $installable[$slug] = $this->model->insert($slug, $moduleAttributes);

                }
            }
        }
        return $installable;
    }

    /**
     * Upgrade Module
     * 
     * @param  string $slug
     * @return bool
     */
    public function upgrade($slug)
    {
        $module = $this->container->instance($slug);

        //@todo check if method 'upgrade' exists
        if ($result = $module->upgrade())
        {
            $this->model->update($slug, ['version' => $module->version]);

            Log::info(sprintf('The module "%s" has been upgraded to version "%s"', $slug, $module->version));

            return true;
        }

        return false;
    }

}
