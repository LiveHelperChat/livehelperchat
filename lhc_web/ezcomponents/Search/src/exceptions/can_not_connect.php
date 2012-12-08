<?php
/**
 * File containing the ezcSearchCanNotConnectException class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This exception is thrown when no connection can be made against a search backend.
 *
 * @package Search
 * @version 1.0.9
 */
class ezcSearchCanNotConnectException extends ezcSearchException
{
    /**
     * Constructs an ezcSearchCanNotConnectException for type $type at location $location
     *
     * @param string $type
     * @param string $location
     * @return void
     */
    public function __construct( $type, $location )
    {
        $message = "Could not connect to '$type' at '$location'.";
        parent::__construct( $message );
    }
}
?>
