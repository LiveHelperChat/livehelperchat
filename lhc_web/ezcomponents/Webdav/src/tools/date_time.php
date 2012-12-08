<?php
/**
 * File containing the ezcWebdavDateTime class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * DateTime class with serialization support.
 *
 * The PHP 5.2 {@link DateTime} class does not support
 * serialization/deserialization with maintaining the stored time information.
 * This class extends DateTime to solve the issue, which is needed especially
 * when working with persistent {@link ezcWebdavMemoryBackend} instances.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavDateTime extends DateTime
{
    /**
     * Stores the backup time in RFC 2822 format when being serialized.
     * 
     * @var string
     */
    private $backupTime;

    /**
     * Backup the currently stored time.
     *
     * This method is called right before serialization of the object. It backs
     * up the current time information as an RCF 2822 formatted string and
     * returns the name of the property this value is stored inside as an array
     * to indicate that this property should be serialized.
     * 
     * @return array(int=>string)
     */
    public function __sleep()
    {
        $this->backupTime = $this->format( 'r' );
        return array( 'backupTime' );
    }

    /**
     * Restores the backeuped time.
     *
     * This method is automatically called after deserializing the object and
     * restores the backed up time information.
     * 
     * @return void
     */
    public function __wakeup()
    {
        $this->__construct( $this->backupTime );
    }
}

?>
