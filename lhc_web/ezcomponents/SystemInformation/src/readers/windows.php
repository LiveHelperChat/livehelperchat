<?php
/**
 * File containing the ezcSystemInfoWindowsReader class
 *
 * @package SystemInformation
 * @version 1.0.8
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Provide functionality to read system information from Windows systems.
 *
 * This reader try to scan Windows system parameters on initialization and fill in
 * correspondent values. CPU parameters are taken from Windows registry.
 * Memory size received using functions in php_win32ps.dll PHP extension.
 *
 * @package SystemInformation
 * @version 1.0.8
 */
class ezcSystemInfoWindowsReader extends ezcSystemInfoReader
{
    /**
     * Contains true if ezcSystemInfoReader object initialized 
     * and system info successfully taken.
     *
     * @var bool
     */
    private $isValid = false;

    /**
     * Contains string that represents reader in messages and exceptions.
     *
     * @var string
     */
    protected $readerName = 'Windows system info reader';

    /**
     * Stores properties that fetched form system once during construction.
     *
     * Read-only after initialization. If property set to true than it contains valid
     * value. Otherwise property is not set.
     *
     * Propertyes could be
     * 'cpu_count'
     * 'cpu_type'
     * 'cpu_speed'
     * 'memory_size'
     *
     * @var array(string)
     */
    private $validProperties = array();

    /**
     * Contains the amount of CPUs in system.
     *
     * @var int
     */
    protected $cpuCount = null;

    /**
     * Contains the strings that represent type of CPU,
     * for each CPU in sysytem. Type is taken directly
     * from the OS and can vary a lot.
     *
     * @var array(string)
     */
    protected $cpuType = null;

    /**
     * Contains the speed of each CPU in MHz.
     *
     * @var array(float)
     */
    protected $cpuSpeed = null;

    /**
     * Contains the amount of system memory the OS has, the value is in bytes.
     *
     * @var int
     */
    protected $memorySize = null;

    /**
     * Constructs ezcSystemInfoReader object and fill it with system information.
     *
     * @throws ezcSystemInfoReaderCantScanOSException
     *         If system variables can't be received from OS.
     */
    public function __construct()
    {
        if ( !$this->getOsInfo() )
        {
            throw new ezcSystemInfoReaderCantScanOSException( "<{$this->readerName}>: can't scan OS for system values." );
        }
    }

    /**
     * Scans the OS and fills in the information internally.
     */
    private function init()
    {
        $this->getOsInfo();
    }

    /**
     * Returns true if the property $propertyName holds a valid value and false otherwise.
     *
     * @param string $propertyName
     * @return bool
     */
    public function isValid( $propertyName )
    {
        return true;
    }

    /**
     * Scans the OS and fills in the information internally.
     * Returns true if it was able to scan the system or false if it failed.
     *
     * @param string $dmesgPath path to the source of system information in OS
     * @return bool
     */
    private function getOsInfo()
    {
        // query contents of CentralProcessor section.
        $output = shell_exec( "reg query HKLM\\HARDWARE\\DESCRIPTION\\SYSTEM\\CentralProcessor" );
        $outputStrings = explode( "\n", $output );
        // In first two items of output strings we have the signature of reg.exe utility 
        // and path to CentralProcessor section than list of subsections paths follows.
        // One subsection represent info for one CPU.
        // Name of each subsection is index of CPU starting from 0.
        if ( is_array( $outputStrings ) && count( $outputStrings ) > 2 )
        {
            $this->cpuCount = count( $outputStrings ) - 2; // cpuCount is amount of subsections, output header skipped.
            for ( $i = 0; $i < $this->cpuCount; $i++ )
            {
                $output = shell_exec( "reg query HKLM\\HARDWARE\\DESCRIPTION\\SYSTEM\\CentralProcessor\\$i /v ProcessorNameString" );
                preg_match( "/ProcessorNameString\s*\S*\s*(.*)/", $output, $matches );
                if ( isset( $matches[1] ) )
                {
                    $this->cpuType[] = $matches[1];
                    $this->validProperties['cpuType'] = $this->cpuType;
                }
                unset( $matches );

                $output = shell_exec( "reg query HKLM\\HARDWARE\\DESCRIPTION\\SYSTEM\\CentralProcessor\\$i /v ~MHz" );
                preg_match( "/~MHz\s*\S*\s*(\S*)/", $output, $matches );
                if ( isset( $matches[1] ) )
                {
                    $this->cpuSpeed[] = (float)hexdec( $matches[1] ).'.0'; // force to be float value
                    $this->validProperties['cpu_count'] = $this->cpuCount;
                    $this->validProperties['cpu_speed'] = $this->cpuSpeed;
                }
                unset( $matches );
            }
        }

        // if no php_win32ps.dll extension installed than scanning of
        // Total Physical memory is not supported.
        // It's could be implemented on WinXP and Win2003 using call to
        // Windows Management Instrumentation (WMI) service like "wmic memphysical"
        // (should be researched in details) or with help of some free third party
        // utility like psinfo.exe from SysInternals ( www.sysinternals.com ).

        if ( ezcBaseFeatures::hasExtensionSupport( 'win32ps' ) )
        {
            $memInfo = win32_ps_stat_mem();
            $this->memorySize = $memInfo['total_phys'] * $memInfo['unit'];
            $this->validProperties['memory_size'] = $this->memorySize;
        }
        return true;
    }

    /**
     * Returns count of CPUs in system.
     *
     * If the CPU speed could not be read null is returned.
     *
     * @return int with count of CPUs in system or null.
     */
    public function getCpuCount()
    {
        return $this->cpuCount;
    }

    /**
     * Returns string with CPU speed.
     *
     * If the CPU speed could not be read null is returned.
     *
     * @return string
     */
    public function cpuSpeed()
    {
        if ( !is_array( $this->cpuSpeed ) || count( $this->cpuSpeed ) == 0 )
        {
            return null;
        }

        $result = null;
        foreach ( $this->cpuSpeed as $speed )
        {
            $result += $speed;
        }

        $result = $result / count( $this->cpuSpeed );
        return $result;
    }

    /**
     * Returns string with CPU type.
     *
     * If the CPU type could not be read null is returned.
     *
     * @return string
     */
    public function cpuType()
    {
        return $this->cpuType[0];
    }

    /**
     * Returns memory size in bytes.
     *
     * If the memory size could not be read null is returned.
     *
     * @return int
     */
    public function memorySize()
    {
        return $this->memorySize;
    }
}
?>
