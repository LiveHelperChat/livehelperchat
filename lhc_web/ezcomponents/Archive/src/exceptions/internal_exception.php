<?php
/**
 * File containing the ezcArchiveInternalException class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception used when an internal errors occurs in the Archive component.
 *
 * @package Archive
 * @version 1.4.1
 */
class ezcArchiveInternalException extends ezcArchiveException
{
    /**
     * Construct an internal archive exception.
     *
     * @param string $message
     */
    public function __construct( $message )
    {
        parent::__construct( "Internal error: " . $message );
    }
}
?>
