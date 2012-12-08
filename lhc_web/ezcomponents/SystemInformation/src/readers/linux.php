<?php
/**
 * File containing the ezcSystemInfoLinuxReader class
 *
 * @package SystemInformation
 * @version 1.0.8
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Provide functionality to read system information from Linux systems.
 *
 * Try to scan Linux system parameters on initialization and fill
 * correspondent values.
 *
 * @package SystemInformation
 * @version 1.0.8
 */
class ezcSystemInfoLinuxReader extends ezcSystemInfoReader
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
    protected $readerName = 'Linux system info reader';

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
     * @param mixed $cpuinfoPath path to the source of cpu information in system
     * @param mixed $meminfoPath path to the source of memory information in system
     * @return bool
     */
    private function getOsInfo( $cpuinfoPath = false, $meminfoPath = false )
    {
        if ( !$cpuinfoPath )
        {
            $cpuinfoPath = '/proc/cpuinfo';
        }
        if ( !$meminfoPath )
        {
            $meminfoPath = '/proc/meminfo';
        }

        if ( !file_exists( $cpuinfoPath ) )
        {
            return false;
        }
        if ( !file_exists( $meminfoPath ) )
        {
            return false;
        }

        $cpuCount = 0;
        $fileLines = file( $cpuinfoPath );
        foreach ( $fileLines as $line )
        {
            if ( substr( $line, 0, 7 ) == 'cpu MHz' )
            {
                $cpu = trim( substr( $line, 11, strlen( $line ) - 11 ) );
                if ( $cpu != '' ) 
                {
                    $this->cpuSpeed[] = (float)$cpu;

                    $cpuCount++;
                    $this->cpuCount = $cpuCount;
                    $this->validProperties['cpu_count'] = $this->cpuCount;
                    $this->validProperties['cpu_speed'] = $this->cpuSpeed;
                }
            }
            if ( substr( $line, 0, 10 ) == 'model name' )
            {
                $system = trim( substr( $line, 13, strlen( $line ) - 13 ) );
                if ( $system != '' ) 
                {
                    $this->cpuType[] = $system;
                    $this->validProperties['cpu_type'] = $this->cpuType;
                }
            }
        }

        $fileLines = file( $meminfoPath );
        foreach ( $fileLines as $line )
        {
            if ( substr( $line, 0, 8 ) == 'MemTotal' )
            {
                $mem = trim( substr( $line, 11, strlen( $line ) - 11 ) );
                $memBytes = $mem;
                if ( preg_match( "#^([0-9]+) *([a-zA-Z]+)#", $mem, $matches ) )
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
            }
            if ( $this->memorySize !== null )
            {
                break;
            }
        }
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
