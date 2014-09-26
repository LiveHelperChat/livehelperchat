<?php
/**
 * File containing the ezcMailInvalidLimitException class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcMailInvalidLimitException is thrown when request is made to
 * fetch messages with the offset outside of the existing message range.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailInvalidLimitException extends ezcMailException
{
    /**
     * Constructs an ezcMailInvalidLimitException
     *
     * @param mixed $offset
     * @param mixed $count
     */
    public function __construct( $offset, $count )
    {
        parent::__construct( "The message count '{$count}' is not allowed for the message subset '{$offset}', '{$count}'." );
    }
}
?>
