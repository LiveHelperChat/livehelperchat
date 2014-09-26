<?php
/**
 * File containing the ezcSystemInfoMacReader class
 *
 * @package SystemInformation
 * @version 1.0.8
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Provide functionality to read system information from Mac OS X systems.
 *
 * Try to scan Mac OS X system parameters on initialization and fill
 * correspondent values.
 *
 * @package SystemInformation
 * @version 1.0.8
 */
class ezcSystemInfoMacReader extends ezcSystemInfoReader
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
    protected $readerName = 'Mac system info reader';

    /**
     * Stores properties that fetched form system once during construction.
     *
     * Read-only after initialization. If property set to true than it contains valid
     * value. Otherwise property is not set.
     *
     * Properties could be
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
        if ( !$this->init() )
        {
            throw new ezcSystemInfoReaderCantScanOSException( "<{$this->readerName}>: can't scan OS for system values." );
        }
    }

    /**
     * Scans the OS and fills in the information internally.
     *
     * @return bool
     */
    private function init()
    {
        return $this->getOsInfo();
    }

    /**
     * Returns true if the property $propertyName holds a valid value and false otherwise.
     *
     * @param string $propertyName
     * @return bool
     */
    public function isValid( $propertyName )
    {
        if ( isset( $validProperties[$propertyName] ) )
        {
            return true;
        }
        return false;
    }

    /**
     * Scans the OS and fills in the information internally.
     *
     * Returns true if it was able to scan the system or false if it failed.
     *
     * @return bool
     */
    private function getOsInfo()
    {
        // System profiler does not have all of these options for older releases than 10.4 of Mac OS X, so we only go forward if we have
        // version 10.4.x and up.
        $macOSXInfo = shell_exec( "defaults read /System/Library/CoreServices/SystemVersion 'ProductVersion'" );
        $versionArray  = explode( ".", $macOSXInfo );
        $compatibleVersion = ( $versionArray[0] == 10 and $versionArray[1] >= 4 );
        if ( !$compatibleVersion )
        {
            return false;
        }
        
        $macInfo = array(); 
        $supportedElements = array ( "cpu_type",
                                     "current_processor_speed", 
                                     // "machine_name", We don't support this yet.
                                     // "number_processors", /* Number CPU cores */ This is not reported as of yet
                                     "packages", /* Number of physical CPUS */
                                     "physical_memory" );
        $allValuesDetected = false;
        
        $hwInfo = shell_exec( "system_profiler -xml -detailLevel mini SPHardwareDataType" );

        $reader = new XMLReader();
        $reader->XML( $hwInfo );

        while ( $reader->read() )
        {
            if ( $reader->nodeType != XMLReader::END_ELEMENT and ( $reader->name == "key" ) )
            {
                $reader->read();
                $key = $reader->value;
                if ( is_string( $key ) and in_array( $key, $supportedElements ) )
                {
                    do
                    {
                        $reader->read();
                        if ( $reader->name == "string" or $reader->name == "integer" )
                        {
                            $reader->read();
                            $macInfo[$key] = $reader->value;
                        }
                    } while ( $reader->nodeType != XMLReader::ELEMENT and $reader->nodeType != XMLReader::TEXT );

                    foreach ( $supportedElements as $element )
                    {
                        $allValuesDetected = array_key_exists( $element, $macInfo );
                    }

                    if ( $allValuesDetected )
                    {
                        break;
                    }
                }
            }
        }
        
        $this->cpuType[] = $macInfo['cpu_type'];
        $this->validProperties['cpu_type'] = $this->cpuType;
        
        $this->cpuCount = (int)$macInfo['packages'];
        $this->validProperties['cpu_count'] = $this->cpuCount;
        
        preg_match( "#^([0-9]+([\.|,][0-9]*)?) *([a-zA-Z]+)#", $macInfo['current_processor_speed'], $matches );
        $cpuSpeed = (float)$matches[1];
        $speedUnit = strtolower( $matches[3] );
        unset( $matches );
        
        // We are displaying in MHz
        if ( $speedUnit == 'mhz' )
        {
            $cpuSpeed *= 1;
        }
        else if ( $speedUnit == 'ghz' )
        {
            $cpuSpeed *= 1000;
        }
        $this->cpuSpeed[] = $cpuSpeed;
        $this->validProperties['cpu_speed'] = $this->cpuSpeed;
        
        if ( preg_match( "#^([0-9]+) *([a-zA-Z]+)#", $macInfo['physical_memory'], $matches ) )
        {
            $memBytes = (int)$matches[1];
            $unit = strtolower( $matches[2] );
            if ( $unit == 'kb' )
            {
                $memBytes *= 1024;
            }
            else if ( $unit == 'mb' )
            {
                $memBytes *= 1024*1024;
            }
            else if ( $unit == 'gb' )
            {
                $memBytes *= 1024*1024*1024;
            }
        }
        else
        {
            $memBytes = (int)$memBytes;
        }
        $this->memorySize = $memBytes;
        $this->validProperties['memory_size'] = $this->memorySize;

        return true;
    }

    /**
     * Returns count of CPUs in system.
     *
     * If the CPU speed could not be read false is returned.
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
     * Average CPU speed returned if there is several CPUs is system
     * If the CPU speed could not be read null is returned.
     *
     * @return float with speed of CPU or null
     */
    public function cpuSpeed()
    {
        $totalSpeed = 0;
        foreach ( $this->cpuSpeed as $speed )
        {
            $totalSpeed += $speed;
        }
        return $totalSpeed/$this->cpuCount;
    }

    /**
     * Returns string with CPU type.
     *
     * If the CPU type could not be read null is returned.
     *
     * @return string the CPU type or null
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
     * @return int the memory size or null
     */
    public function memorySize()
    {
        return $this->memorySize;
    }
}
?>
