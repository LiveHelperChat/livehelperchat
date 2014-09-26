<?php
/**
 * File containing the ezcMailNoSuchMessageException class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcMailNoSuchMessageException is thrown when a message with an ID is
 * requested that doesn't exist in the transport.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailNoSuchMessageException extends ezcMailException
{
    /**
     * Constructs an ezcMailNoSuchMessageException
     *
     * @param mixed $messageId
     */
    public function __construct( $messageId )
    {
        parent::__construct( "The message with ID '{$messageId}' could not be found." );
    }
}
?>
