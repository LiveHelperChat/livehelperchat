<?php
/**
 * File containing the ezcSystemInfoReader class
 *
 * @package SystemInformation
 * @version 1.0.8
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcSystemInfoReader represents common interface of OS info reader.
 *
 * @package SystemInformation
 * @version 1.0.8
 */
abstract class ezcSystemInfoReader
{
    /**
     * Returns true if the property $propertyName holds a valid value and false otherwise.
     *
     * @param string $propertyName
     * @return bool
     */
    abstract public function isValid( $propertyName );

    /**
     * Returns number of CPUs in system.
     *
     * @return int the number of CPUs in system or null if number of CPUs is unknown.
     */
    abstract public function getCpuCount();

    /**
     * Returns string with CPU type.
     *
     * @return string the CPU type or null if the CPU type is unknown.
     */
    abstract public function cpuType();

    /**
     * Returns CPU speed
     *
     * If the CPU speed could not be read null is returned.
     * Average processor speed returned for multiprocessor systems.
     *
     * @return float the CPU speed or null if the CPU speed is unknown.
     */
    abstract public function cpuSpeed();

    /**
     * Returns memory size in bytes.
     *
     * If the memory size could not be read null is returned.
     *
     * @return int the memory size in bytes or null
     */
    abstract public function memorySize();
}
?>
