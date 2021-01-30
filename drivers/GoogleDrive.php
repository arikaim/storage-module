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

use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\AdapterInterface;

use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Core\Storage\StorageDriverInterface;
use Exception;

/**
 * Google drive flysystem driver class
 */
class GoogleDrive implements DriverInterface, StorageDriverInterface
{   
    use Driver;
   
    /**
     * Filesystem obj ref
     *
     * @var Filesystem
     */
    protected $filesystem = null;

    /**
     * Adapter
     *
     * @var AdapterInterface
     */
    protected $adapter = null;

    /**
     * Root ftp path
     *
     * @var string
     */
    protected $rootPath = '/';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setDriverParams('google-drive','flysystem','Google Drive','Driver for Google Drive filesystem.');       
    }

    /**
     * Init driver
     *
     * @param Properties $properties
     * @return void
     */
    public function initDriver($properties)
    {
        $this->rootPath = $properties->getValue('root');

        $client = new \Google_Client();
        $client->setClientId($properties->getValue('client_id'));
        $client->setClientSecret($properties->getValue('client_secret'));
        $client->refreshToken($properties->getValue('refresh_roken'));

        $service = new \Google_Service_Drive($client);

        $this->adapter = new GoogleDriveAdapter($service,null);

        $this->filesystem = new Filesystem($this->adapter);
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
            $result = $this->adapter->listContents($this->getRootPath(),false);            
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return (\is_array($result) == true) ? 1 : 0;
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
        // key
        $properties->property('client_id',function($property) {
            $property
                ->title('Client Id')
                ->type('text')
                ->required(true)
                ->default('');
        });
        // secret
        $properties->property('client_secret',function($property) {
            $property
                ->title('Client Secret')
                ->type('text')  
                ->required(true)         
                ->default('');
        });
        // refresh_roken
        $properties->property('refresh_roken',function($property) {
            $property
                ->title('Refresh Token')
                ->type('text')     
                ->required(true)      
                ->default('');
        });
    }
}
