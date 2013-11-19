<?php namespace Ryun\Module;

use Illuminate\Filesystem\Filesystem;

/**
 * Module StoreInterface
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
