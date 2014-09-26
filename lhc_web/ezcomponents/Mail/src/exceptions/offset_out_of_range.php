<?php
/**
 * File containing the ezcMailOffsetOutOfRangeException class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcMailOffsetOutOfRangeException is thrown when request is made to
 * fetch messages with the offset outside of the existing message range.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailOffsetOutOfRangeException extends ezcMailException
{
    /**
     * Constructs an ezcMailOffsetOutOfRangeException
     *
     * @param mixed $offset
     * @param mixed $count
     */
    public function __construct( $offset, $count )
    {
        parent::__construct( "The offset '{$offset}' is outside of the message subset '{$offset}', '{$count}'." );
    }
}
?>
