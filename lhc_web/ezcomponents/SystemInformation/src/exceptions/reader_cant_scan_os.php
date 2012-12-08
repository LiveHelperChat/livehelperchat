<?php
/**
 * File containing the ezcSystemInfoReaderCantScanOSException class
 * 
 * @package SystemInformation
 * @version 1.0.8
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Reader can't scan OS for system values exception.
 *
 * Throws when system information source (file, registry, etc.)
 * is not available.
 *
 * @package SystemInformation
 * @version 1.0.8
 */
class ezcSystemInfoReaderCantScanOSException extends ezcSystemInfoException
{
    /**
     * Construct a reader can't scan OS exception.
     *
     * @param string $msg
     */
    function __construct( $msg )
    {
        parent::__construct( $msg );
    }
}
?>
