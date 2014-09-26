<?php
/**
 * File containing the ezcSystemInfoFreeBsdReader class
 *
 * @package SystemInformation
 * @version 1.0.8
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Provide functionality to read system information from FreeBSD systems.
 *
 * Try to scan FreeBSD system parameters on initialization and fill
 * correspondent values.
 *
 * @package SystemInformation
 * @version 1.0.8
 */
class ezcSystemInfoFreeBsdReader extends ezcSystemInfoReader
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
    protected $readerName = 'FreeBSD system info reader';

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
     * Contains the type of CPU for each CPU in system, the type is taken directly from the OS
     * and can vary a lot.
     *
     * @var array(string)
     */
    protected $cpuType = null;

    /**
     * Contains the speed of CPU in MHz for each CPU in system.
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
     * Returns true if it was able to scan the system or false if it failed.
     *
     * @param mixed $dmesgPath path to the source of system information in OS
     * @return bool
     */
    private function getOsInfo( $dmesgPath = false )
    {
        if ( !$dmesgPath )
        {
            $dmesgPath = '/var/run/dmesg.boot';
        }
        if ( !file_exists( $dmesgPath ) )
        {
            return false;
        }
        $fileLines = file( $dmesgPath );
        $haveMultiprocessors = false;
        foreach ( $fileLines as $line )
        {
            if ( substr( $line, 0, 3 ) == 'CPU' )
            {
                $system = trim( substr( $line, 4, strlen( $line ) - 4 ) );
                $cpu = null;
                // we should have line like "CPU: AMD Duron(tm)  (1800.07-MHz 686-class CPU)" parse it.
                if ( preg_match( "#^(.+)\\((.+)-(MHz) +([^)]+)\\)#", $system, $matches ) )
                {
                    $system = trim( $matches[1] ) . ' (' . trim( $matches[4] ) . ')';
                    $cpu = $matches[2];
                    $cpuunit = $matches[3];
                }
                $this->cpuSpeed[] = (float)$cpu;
                $this->cpuType[] = $system;
                $this->validProperties['cpu_speed'] = $this->cpuSpeed;
                $this->validProperties['cpu_type'] = $this->cpuType;
                $this->cpuCount = 1;

            }
            if ( substr( $line, 0, 44 ) == 'FreeBSD/SMP: Multiprocessor System Detected:' )
            {
                $multiCpu = trim( substr( $line, 44, strlen( $line ) - 44 ) );
                unset( $matches );
                // we should have line like "FreeBSD/SMP: Multiprocessor System Detected: 4 CPUs" parse it.
                if ( preg_match( "#^([0-9]+).+#", $multiCpu, $matches ) )
                {
                   $this->cpuCount = (int)$matches[1];
                }
                $haveMultiprocessors = true;
            }

            if ( substr( $line, 0, 11 ) == 'real memory' )
            {
                $mem = trim( substr( $line, 12, strlen( $line ) - 12 ) );
                $memBytes = $mem;
                if ( preg_match( "#^= *([0-9]+)#", $mem, $matches ) )
                {
                    $memBytes = $matches[1];
                }
                $memBytes = (int)$memBytes;
                $this->memorySize = $memBytes;
                $this->validProperties['memory_size'] = $this->memorySize;
            }

            // read dmesg until get all necessary info.
            if ( $this->cpuSpeed !== null and
                 $this->cpuType !== null and
                 $this->memorySize !== null and 
                 $haveMultiprocessors !== false )
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
     * If the CPU speed could not be read null is returned.
     *
     * @return float or null
     */
    public function cpuSpeed()
    {
        return $this->cpuSpeed[0];
    }

    /**
     * Returns string with CPU type.
     *
     * If the CPU type could not be read null is returned.
     *
     * @return string or null
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
     * @return int or null
     */
    public function memorySize()
    {
        return $this->memorySize;
    }
}
?>
