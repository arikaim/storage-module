<?php
/**
 * Arikaim
 *
 * @link        http://www.arikaim.com
 * @copyright   Copyright (c)  Konstantin Atanasov <info@arikaim.com>
 * @license     http://www.arikaim.com/license
 * 
*/
namespace Arikaim\Modules\Storage;

use Arikaim\Core\Extension\Module;

/**
 * Storage module class
 */
class Storage extends Module
{
    /**
     * Constructor
     */
    public function __construct()
    {  
    }

    /**
     * Install module
     *
     * @return boolean
     */
    public function install()
    {
        $this->installDriver('Arikaim\\Modules\\Storage\\Drivers\\FtpDriver');       
        $this->installDriver('Arikaim\\Modules\\Storage\\Drivers\\DoSpacesDriver');       
        $this->installDriver('Arikaim\\Modules\\Storage\\Drivers\\AwsS3Driver');    
        $this->installDriver('Arikaim\\Modules\\Storage\\Drivers\\GoogleDrive');    
        $this->installDriver('Arikaim\\Modules\\Storage\\Drivers\\DropboxDriver');    

        return true;
    }
}
