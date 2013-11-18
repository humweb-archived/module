<?php namespace Ryun\Module;

use Illuminate\Filesystem\Filesystem;

/**
 * Module StoreInterface
 * 
 */
class FileStore extends ArrayStore
{

    protected $file;
    protected $filepath;

    /**
     * The array of stored values.
     *
     * @var array
     */
    protected $storage = array();

    public function __construct(Filesystem $file, $filepath = '', array $data = [])
    {
        parent::__construct($data);
        
        $this->file = $file;
        $this->filepath = $filepath;

        $data = $this->getFileContents();

        if ( ! is_array($this->storage))
        {
            $this->storage = [];
        }

        $this->storage = array_merge($this->storage, $data);

    }

    public function __destruct()
    {
        $this->writeFileContents();
    }

    /**
     * Update a specific module record
     * 
     * @param  string $slug
     * @param  array $attributes
     * @return bool
     */
    public function update($slug, array $attributes = [])
    {
        return $this->storage[$slug] = $attributes;
    }

    /**
     * Insert module record
     * 
     * @param  array $attributes
     * @return bool
     */
    public function insert(array $attributes = [])
    {
        return $this->update($slug, $attributes);
    }

    /**
     * Delete module record
     * 
     * @param  string $slug
     * @return bool
     */
    public function delete($slug)
    {
        return unset($this->storage[$slug])
    }

    /**
     * Find a single module record by slug id
     * 
     * @param  string $slug
     * @return array
     */
    public function find($slug)
    {
        if (isset($this->storage[$slug]))
        {
            return $this->storage[$slug];
        }
    }
    
    /**
     * Fetch only modules that are enabled
     * 
     * @return array
     */
    public function getEnabled()
    {
        return array_filter($this->storage, function($val)
        {
            return $val['status'] === ProviderInterface::STATUS_INSTALLED;
        });
    }

    /**
     * Fetch only modules that are installed
     * 
     * @return array
     */
    public function getInstalled()
    {
        return array_filter($this->storage, function($val)
        {
            return $val['status'] ==! ProviderInterface::STATUS_DISABLED;
        });
    }

    /**
     * Fetch only modules that are upgradable
     * 
     * @return array
     */
    public function getUpgradable()
    {
        return array_filter($this->storage, function($val)
        {
            return $val['status'] === ProviderInterface::STATUS_UPGRADE;
        });
    }


    protected function getFileContents()
    {
        return $this->file->getRequire($this->filepath);
    }

    protected function writeFileContents()
    {
        $contents = "<?php\n return ".var_export($this->storage, true) .';';
        return $this->file->put($this->filepath, $contents);
    }
}
