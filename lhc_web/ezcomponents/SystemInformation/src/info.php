<?php
/**
 * File containing the ezcSystemInfo class.
 *
 * @package SystemInformation
 * @version 1.0.8
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Provides access to common system variables.
 *
 * Variables not available from PHP directly are fetched using readers
 * specific for each supported system. Corresponding reader is automatically
 * detected, attached and forced to scan system info during initialization.
 * An exception is thrown if the reader can't scan the system info.
 *
 * Available readers are:
 * - {@link ezcSystemInfoLinuxReader} reader
 * - {@link ezcSystemInfoMacReader} reader
 * - {@link ezcSystemInfoFreeBsdReader} reader
 * - {@link ezcSystemInfoWindowsReader} reader
 *
 * Readers for other systems can be added by
 * implementing the {@link ezcSystemInfoReader} interface.
 *
 * The ezcSystemInfo class has the following properties:
 *
 * Reader independent, these properties are available even if system reader was not initialized.
 * @property string $osType 
 *           OS type (e.g 'unix') or null.
 * @property string $osName
 *           OS name (e.g 'Linux') or null.
 * @property string $fileSystemType
 *           Filesystem type (e.g 'linux') or null.
 * @property string $lineSeparator
 *           Symbols which is used for line separators on the current OS.
 * @property string $backupFileName
 *           Backup filename for this platform, '.bak' for win32 and '~' for unix and mac.
 * @property array $phpVersion
 *           Array with PHP version (e.g. array(5,1,1) ).
 * @property ezcSystemInfoAccelerator $phpAccelerator
 *           Structure with PHP accelerator info or null.
 * {@link ezcSystemInfoAccelerator}.
 * @property  bool $isShellExecution
 *           The flag which indicates if the script was executed over the web or the shell/command line.
 *
 * Reader dependent, these properties are not available if reader was not initialized and didn't scan OS:
 * @property integer $cpuCount
 *           Number of CPUs in system or null.
 * @property string $cpuType 
 *           CPU type string (e.g 'AMD Sempron(tm) Processor 3000+') or null.
 * @property float $cpuSpeed 
 *           CPU speed as float (e.g 1808.743) in Mhz or null.
 * @property integer $memorySize
 *           Memory Size in bytes int (e.g. 528424960) or null.
 *
 * Example:
 *  <code>
 *  $info = ezcSystemInfo::getInstance();
 *  echo 'Processors: ', $info->cpuCount, "\n";
 *  echo 'CPU Type: ', $info->cpuType, "\n";
 *  echo 'CPU Speed: ', $info->cpuSpeed, "\n";
 *  </code>
 *
 * @package SystemInformation
 * @version 1.0.8
 * @mainclass
 */
class ezcSystemInfo
{
    /**
     * Instance of the singleton ezcSystemInfo object.
     *
     * Use the getInstance() method to retrieve the instance.
     *
     * @var ezcSystemInfo
     */
    private static $instance = null;

    /**
     * Contains object that provide info about the underlying OS.
     *
     * @var ezcSystemInfoReader
     */
    private $systemInfoReader = null;

    /**
     * Contains string with the type of the underlying OS
     * or empty string if OS can't be detected.
     *
     * @var string
     */
    private $osType = null;

    /**
     * Contains string with the name of the underlying OS
     * or empty string if OS can't be detected.
     *
     * @var string
     */
    private $osName = null;

    /**
     * Contains string with the filesystem type of the underlying OS
     * or empty string if OS can't be detected.
     *
     * @var string
     */
    private $fileSystemType = null;

    /**
     * Contains string with the line separator of the underlying OS
     * or empty string if OS can't be detected.
     *
     * @var string
     */
    private $lineSeparator = null;

    /**
     * Contains string with the backup file name of the underlying OS
     * or empty string if OS can't be detected.
     *
     * @var string
     */
    private $backupFileName = null;

    /**
     * Returns the single instance of the ezcSystemInfo class.
     *
     * @throws ezcSystemInfoReaderCantScanOSException
     *         If system variables can't be received from OS.
     * @return ezcSystemInfo
     */
    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructs ezcSystemInfo object, inits it with corresponding underlying OS data.
     *
     * @throws ezcSystemInfoReaderCantScanOSException
     *         If system variables can't be received from OS.
     */
    private function __construct()
    {
        $this->init();
    }

    /**
     * Detects underlying system and sets system properties.
     *
     * @throws ezcSystemInfoReaderCantScanOSException
     *         If system variables can't be received from OS.
     */
    private function init()
    {
        $this->setSystemInfoReader();
    }

    /**
     * Sets the systemInfoReader depending on the OS and fills in the system
     * information internally.
     *
     * Returns true if it was able to set appropriate systemInfoReader
     * or false if failed.
     *
     * @throws ezcSystemInfoReaderCantScanOSException
     *         If system variables can't be received from OS.
     * @return bool
     */
    private function setSystemInfoReader()
    {
        // Determine OS
        $uname = php_uname( 's' );

        if ( substr( $uname, 0, 7 ) == 'Windows' )
        {
            $this->systemInfoReader = new ezcSystemInfoWindowsReader( $uname );
            $this->osType = 'win32';
            $this->osName = 'Windows';
            $this->fileSystemType = 'win32';
            $this->lineSeparator= "\r\n";
            $this->backupFileName = '.bak';
        }
        else if ( substr( $uname, 0, 6 ) == 'Darwin' )
        {
            $this->systemInfoReader = new ezcSystemInfoMacReader();
            $this->osType = 'mac';
            $this->osName = 'Mac OS X';
            $this->fileSystemType = 'unix';
            $this->lineSeparator= "\n";
            $this->backupFileName = '~';
        }
        else
        {
            $this->osType = 'unix';
            if ( strtolower( $uname ) == 'linux' )
            {
                $this->systemInfoReader = new ezcSystemInfoLinuxReader();
                $this->osName = 'Linux';
                $this->fileSystemType = 'unix';
                $this->lineSeparator= "\n";
                $this->backupFileName = '~';
            }
            else if ( strtolower( substr( $uname, 0, 7 ) ) == 'freebsd' )
            {
                $this->systemInfoReader = new ezcSystemInfoFreeBsdReader();
                $this->osName = 'FreeBSD';
                $this->fileSystemType = 'unix';
                $this->lineSeparator= "\n";
                $this->backupFileName = '~';
            }
            else
            {
                $this->systemInfoReader = null;
                return false;
            }
        }
        return true;
    }

    /**
     * Detects if a PHP accelerator is running and what type it is.
     *
     * @return ezcSystemInfoAccelerator or null if no PHP accelerator detected
     */
    public static function phpAccelerator()
    {
        $phpAcceleratorInfo = null;
        if ( ezcBaseFeatures::hasExtensionSupport( "Turck MMCache" ) )
        {
            $phpAcceleratorInfo = new ezcSystemInfoAccelerator(
                    "Turck MMCache",                        // name
                    "http://turck-mmcache.sourceforge.net", // url
                    true,                                   // isEnabled
                    false,                                  // version int
                    false                                   // version string
                );
        }
        if ( ezcBaseFeatures::hasExtensionSupport( "eAccelerator" ) )
        {
            $phpAcceleratorInfo = new ezcSystemInfoAccelerator(
                    "eAccelerator",                                     // name
                    "http://sourceforge.net/projects/eaccelerator/",    // url
                    true,                                               // isEnabled
                    false,                                              // version int
                    phpversion( 'eAccelerator' )                        // version string
                );
        }
        if ( ezcBaseFeatures::hasExtensionSupport( "apc" ) )
        {
            $phpAcceleratorInfo = new ezcSystemInfoAccelerator(
                    "APC",                                  // name
                    "http://pecl.php.net/package/APC",      // url
                    ( ini_get( 'apc.enabled' ) != 0 ),      // isEnabled
                    false,                                  // version int
                    phpversion( 'apc' )                     // version string
                );
        }
        if ( ezcBaseFeatures::hasExtensionSupport( "Zend Performance Suite" ) )
        {
            $phpAcceleratorInfo = new ezcSystemInfoAccelerator(
                    "Zend Performance Suite",                                  // name
                    "http://www.zend.com/en/products/platform/",               // url
                    true,                                                      // isEnabled
                    false,                                                     // version int
                    false                                                      // version string
                );
        }
        if ( ezcBaseFeatures::hasExtensionSupport( 'XCache' ) )
        {
            $phpAcceleratorInfo = new ezcSystemInfoAccelerator(
                    "XCache",                               // name
                    "http://xcache.lighttpd.net/",          // url
                    true,                                   // isEnabled
                    false,                                  // version int
                    phpversion( 'XCache' )                  // version string
                );
        }

        return $phpAcceleratorInfo;
    }

    /**
     * Determines if the script was executed over the web or the shell/command line.
     *
     * @return bool
     */
    public static function isShellExecution()
    {
        $sapiType = php_sapi_name();

        if ( $sapiType == 'cli' )
        {
            return true;
        }

        // For CGI we have to check, if the script has been executed over shell.
        // Currently it looks like the HTTP_HOST variable is the most reasonable to check.
        if ( substr( $sapiType, 0, 3 ) == 'cgi' )
        {
            if ( !isset( $_SERVER['HTTP_HOST'] ) )
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        return false;
    }

    /**
     * Returns the PHP version as an array with the version elements.
     *
     * @return array(string)
     */
    public static function phpVersion()
    {
        return explode( '.', phpVersion() );
    }

    /**
     * Property read access.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     * @param string $property Name of the property.
     * @return mixed Value of the property or null.
     * @ignore
     */
    public function __get( $property )
    {
        if ( $this->systemInfoReader == null &&
             ( $property == 'cpuType'  ||
               $property == 'cpuCount' ||
               $property == 'cpuSpeed' ||
               $property == 'memorySize'
             )
           )
        {
            return null;
        }

        switch ( $property )
        {
            case 'osType':
                return $this->osType;
            case 'osName':
                return $this->osName;
            case 'fileSystemType':
                return $this->fileSystemType;
            case 'cpuCount':
                return $this->systemInfoReader->getCpuCount();
            case 'cpuType':
                return $this->systemInfoReader->cpuType();
            case 'cpuSpeed':
                return $this->systemInfoReader->cpuSpeed();
            case 'memorySize':
                return $this->systemInfoReader->memorySize();
            case 'lineSeparator':
                return $this->lineSeparator;
            case 'backupFileName':
                return $this->backupFileName;
            case 'phpVersion':
                return $this->phpVersion();
            case 'phpAccelerator':
                return $this->phpAccelerator();
            case 'isShellExecution':
                return $this->isShellExecution();

            default:
                break;
        }
        throw new ezcBasePropertyNotFoundException( $property );
    }
}
?>
