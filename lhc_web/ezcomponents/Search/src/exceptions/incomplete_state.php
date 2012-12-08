<?php
/**
 * File containing the ezcSearchIncompleteStateException class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown when a field from the definition was not returned by
 * getState().
 *
 * @package Search
 * @version 1.0.9
 */
class ezcSearchIncompleteStateException extends ezcSearchException
{
    /**
     * Constructs an ezcSearchIncompleteStateException for field $field.
     *
     * @param string $field
     */
    public function __construct( $field )
    {
        $message = "The getState() method did not return any value for the field '$field'.";
        parent::__construct( $message );
    }
}
?>
