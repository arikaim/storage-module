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

use League\Flysystem\Sftp\SftpAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\AdapterInterface;

use Arikaim\Core\Driver\Traits\Driver;
use Arikaim\Core\Interfaces\Driver\DriverInterface;
use Arikaim\Core\Storage\StorageDriverInterface;
use Exception;

/**
 * Sftp flysystem driver class
 */
class SftpDriver implements DriverInterface, StorageDriverInterface
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
     * @var AdapterInterface|object
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
        $this->setDriverParams('sftp','flysystem','SFtp','Driver for sftp storage filesystem');       
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

        $this->adapter = new SftpAdapter([
            'host'          => $properties->getValue('host'),
            'username'      => $properties->getValue('username'),
            'password'      => $properties->getValue('password'),          
            'port'          => $properties->getValue('port'),
            'root'          => $properties->getValue('root'),
            'passphrase'    => (bool)$properties->getValue('passphrase'),
            'privateKey'    => (bool)$properties->getValue('private_key'),
            'timeout'       => (int)$properties->getValue('timeout'),
            'directoryPerm' => 0755
        ]);

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
            $this->adapter->connect();
            return ($this->adapter->isConnected() == true) ? 1 : false;
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
        // ftp host
        $properties->property('host',function($property) {
            $property
                ->title('Host')
                ->type('text')
                ->default('');
        });
        // username
        $properties->property('username',function($property) {
            $property
                ->title('Username')
                ->type('text')           
                ->default('');
        });
        // password
        $properties->property('password',function($property) {
            $property
                ->title('Password')
                ->type('password')           
                ->default('');
        });
        // port
        $properties->property('port',function($property) {
            $property
                ->title('Port')
                ->type('number')
                ->default(21);
        });
        // Root path
        $properties->property('root',function($property) {
            $property
                ->title('Root path')
                ->type('text')
                ->default('/');
        });       
        // passive
        $properties->property('passphrase',function($property) {
            $property
                ->title('Passphrase')
                ->type('text')
                ->value('')
                ->default('');
        });
        // timeout
        $properties->property('timeout',function($property) {
            $property
                ->title('Timeout')
                ->type('number')
                ->default(30);
        });
        // ignorePassiveAddress
        $properties->property('private_key',function($property) {
            $property
                ->title('Private Key')
                ->type('text')
                ->default('')
                ->value('');
        });
    }
}
