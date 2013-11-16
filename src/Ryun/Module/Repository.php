<?php namespace Ryun\Module;
 
use Carbon\Carbon;

/**
 * Module Repository class
 * 
 */
class Repository
{
    protected $model;
    protected $fileloader;

    /**
     * Create a new repository instance
     * 
     * @param Ryun\Module\Provider $provider
     * @param Ryun\Module\LoaderInterface $fileloader
     * @param Ryun\Module\StoreInterface $model
     */
    public function __construct(Provider $provider = null, LoaderInterface $fileloader = null, StoreInterface $model = null)
    {
        $this->fileloader = $fileloader;
        $this->provider = $provider;
        $this->model = $model;
    }

    /**
     * Find a single entry
     * 
     * @param  string $slug
     * @return array
     */
    public function find($slug)
    {
        return $this->model->findBy('slug', $slug);
    }
 
    /**
     * Fetch only modules that are installed
     * 
     * @return array
     */
    public function getInstalled()
    {
        return $this->model->findBy('status', ProviderInterface::STATUS_DISABLED, '!=');
    }

    /**
     * Fetch only modules that are enabled
     * 
     * @return array
     */
    public function getEnabled()
    {
        return $this->model->findBy('status', ProviderInterface::STATUS_INSTALLED);
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
        return $this->setStatus($slug, ProviderInterface::STATUS_INSTALLED);
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
     * @param [type] $slug   [description]
     * @param [type] $status [description]
     */
    protected function setStatus($slug, $status)
    {
        return $this->model->update($slug, ['status' => $status]);
    }

    //-------------------------------------------------------------
    // Import new modules to data store
    //-------------------------------------------------------------
    public function import_unknown()
    {
        $modules = array();
        $return = array();

        $is_core = true;

        //$known = static::get();
        $modulesInstalled = $this->model->fetchInstalled();

        $installedKeys = array();
        $installedObjects = array();

        // Format array to be more friendly
        if (count($modulesInstalled) > 0)
        {
            foreach ($modulesInstalled as $module)
            {
                $installedKeys[] = $module->slug;
                $installedObjects[$module->slug] = $item;
            }
        }

        //Installed Modules
        foreach ($this->fileloader->getFolders() as $path)
        {
            $slug = last(explode('/', str_replace('\\', '/', $path)));

            $meta = $this->provider->instance($slug);

            // These modules are known
            // So we check for upgrades
            if (in_array($slug, $installedKeys) and $meta = $this->provider->instance($slug))
            {

                $return[$slug] = [
                    'name'        => $meta->name,
                    'slug'        => $slug,
                    'description' => $meta->description,
                    'status'      => $installedObjects[$slug]->status,
                ];

                //Compare versions and update if needed
                if (version_compare($meta->version, $installedObjects[$slug]->version) == 1)
                {
                    // @todo Update db

                    $return[$slug]['status'] = ProviderInterface::STATUS_UPGRADE;
                    $this->model->update($slug, $return[$slug]);
                    Log::info(sprintf('The information of the module "%s" has been updated', $slug));
                }
            }

            //Add new module to db
            else {
                
                if ( $meta = $this->provider->instance($slug))
                {
                    $return[$slug] = [
                        'name'        => $meta->name,
                        'slug'        => $slug,
                        'version'     => $meta->version,
                        'description' => $meta->description,
                        'status'      => ProviderInterface::STATUS_DISABLED,
                        'updated_on'  => Carbon::now()->format('Y-m-d'),
                    ];
                    $this->model->create($return[$slug]);
                }
            }
        }

        return $return;
    }

    /**
     * Upgrade Module
     * 
     * @param  string $slug
     * @return bool
     */
    public function upgrade($slug)
    {
        $module = $this->provider->instance($slug);

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
