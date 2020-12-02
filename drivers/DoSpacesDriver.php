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

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\AdapterInterface;

use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Core\Storage\StorageDriverInterface;
use Exception;

/**
 * DO spaces flysystem driver class
 */
class DoSpacesDriver implements DriverInterface, StorageDriverInterface
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
        $this->setDriverParams('do-spaces','flysystem','DO spaces filesystem','Driver for Digital Ocean spaces storage filesystem.');       
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

        $client = new S3Client([
            'driver' => 's3',
            'credentials' => [
                'key'    => \trim($properties->getValue('key')),
                'secret' => \trim($properties->getValue('secret'))     
            ],                 
            'region'   => $properties->getValue('region'),
            'version'  => $properties->getValue('version'),
            'endpoint' => $properties->getValue('endpoint')           
        ]);
        
        $this->adapter = new AwsS3Adapter($client,$properties->getValue('bucket_name'),$this->rootPath);
        $this->filesystem = new Filesystem($this->adapter);
    }

    /**
     * Get root path
     *
     * @return string
     */
    public function getRootPath()
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
            return 0;
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
        $properties->property('key',function($property) {
            $property
                ->title('Api Key')
                ->type('text')
                ->default('');
        });
        // secret
        $properties->property('secret',function($property) {
            $property
                ->title('Api Secret')
                ->type('text')           
                ->default('');
        });
        // region
        $properties->property('region',function($property) {
            $property
                ->title('Region')
                ->type('text')           
                ->default('');
        });
        // version
        $properties->property('version',function($property) {
            $property
                ->title('Version')
                ->type('text')
                ->readonly(true)
                ->default('latest');
        });
        // Root path
        $properties->property('root',function($property) {
            $property
                ->title('Root Path')
                ->type('text')
                ->readonly(true)
                ->default('/');
        });
        // bucket_name
        $properties->property('bucket_name',function($property) {
            $property
                ->title('Bucket name')
                ->type('text');                
        });
        // endpoint
        $properties->property('endpoint',function($property) {
            $property
                ->title('Endpoint')
                ->type('text');                
        });
    }
}
