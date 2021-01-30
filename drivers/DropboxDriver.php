<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Storage\Drivers;

use League\Flysystem\Filesystem;
use League\Flysystem\AdapterInterface;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Core\Storage\StorageDriverInterface;
use Exception;

/**
 * Dropbox flysystem driver class
 */
class DropboxDriver implements DriverInterface, StorageDriverInterface
{   
    use Driver;
   
    /**
     * Filesystem obj ref
     *
     * @var Filesystem|null
     */
    protected $filesystem = null;

    /**
     * Adapter
     *
     * @var AdapterInterface|object
     */
    protected $adapter = null;

    /**
     * Root ftp path
     *
     * @var string
     */
    protected $rootPath = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDriverParams('dropbox','flysystem','Dropbox','Driver for Dropbox filesystem.');       
    }

    /**
     * Init driver
     *
     * @param Properties $properties
     * @return void
     */
    public function initDriver($properties)
    {
        $accessToken = $properties->getValue('access_token');
        $client = new Client($accessToken);
        $this->adapter = new DropboxAdapter($client);
        $this->filesystem = new Filesystem($this->adapter,['case_sensitive' => false]);
    }

    /**
     * Get root path
     *
     * @return string
     */
    public function getRootPath(): ?string
    {
        return $this->rootPath;
    }

    /**
     * Check filesystem adapter connection
     *
     * @return string|int
     */
    public function checkConnection() {
        if (empty($this->adapter) == true || empty($this->filesystem) == true) {
            return false;
        }

        try {
            $result = $this->adapter->getClient()->listFolder('',false);
        
            return (empty($result) === false) ? 1 : 'Error Dropbox api connection';              
        } catch (Exception $e) {
            return $e->getMessage();
        } 

        return 1;
    }

    /**
     * Get filesystem
     *
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * Get adapter
     *
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Create driver config properties array
     *
     * @param Arikaim\Core\Collection\Properties $properties
     * @return array
     */
    public function createDriverConfig($properties)
    {              
        // Access token
        $properties->property('access_token',function($property) {
            $property
                ->title('Access token')
                ->type('text')
                ->required(true)
                ->default('');
        });
    }
}
