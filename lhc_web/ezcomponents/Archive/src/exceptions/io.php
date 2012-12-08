<?php
/**
 * File containing the ezcArchiveIoException class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown when an IO error occurs.
 *
 * @package Archive
 * @version 1.4.1
 */
class ezcArchiveIoException extends ezcArchiveException
{
    /**
     * Constructs a new IO exception.
     *
     * @param string $message
     */
    public function __construct( $message )
    {
        parent::__construct( $message );
    }
}
?>
