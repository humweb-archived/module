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
     * @param Humweb\Module\Provider $provider
     * @param Humweb\Module\LoaderInterface $fileloader
     * @param Humweb\Module\StoreInterface $model
     */
    public function __construct(ProviderInterface $provider = null, FileLoaderInterface $fileloader = null, StoreInterface $model = null)
    {
        $this->fileloader = $fileloader;
        $this->provider = $provider;
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

    public function importUnknown()
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

                if ( $module->status == ProviderInterface::STATUS_INSTALLED)
                {
                    $installedKeys[] = $module->slug;
                }
                else {
                    $installable[$module->slug] = $module;
                }

            }
        }

        //Installed Modules
        foreach ($this->fileloader->getFolders() as $path)
        {
            $slug = last(explode('/', str_replace('\\', '/', $path)));

            $meta = $this->provider->instance($slug);

            // These modules are known
            // So we check for upgrades
            if (in_array($slug, $knownKeys) and $meta)
            {

                $return[$slug] = [
//                    'name'        => $meta->name,
//                    'slug'        => $slug,
//                    'description' => $meta->description,
//                    'status'      => $installedObjects[$slug]->status,
                ];

                $hasNewerVersion = version_compare($modules[$slug]->version, $meta->version, '<');

                if ($modules[$slug]->status == ProviderInterface::STATUS_INSTALL)
                {

                    $installable[$modules[$slug]->slug] = $modules[$slug];

                    if ($hasNewerVersion)
                    {
                        $this->model->update($slug, ['version' =>  $meta->version]);
                    }

                }

                //Compare versions and update if needed
                elseif ($hasNewerVersion and $modules[$slug]->status != ProviderInterface::STATUS_DISABLED)
                {
                    $return[$slug]['status'] = ProviderInterface::STATUS_UPGRADE;
                    $this->model->update($slug, $return[$slug]);
                    \Log::info(sprintf('The information of the module "%s" has been updated', $slug));
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
                        'updated_on'  => Carbon::now(),
                    ];
                    $installable[$slug] = $this->model->insert($slug, $return[$slug]);
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
