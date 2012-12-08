<?php
/**
 * File containing the ezcSignalSlotException class
 *
 * @package SignalSlot
 * @version 1.1.1
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * ezcSignalSlotExceptions are thrown when an exceptional state
 * occures in the SignalSlot package.
 *
 * @package SignalSlot
 * @version 1.1.1
 */
class ezcSignalSlotException extends ezcBaseException
{
    /**
     * Constructs a new ezcSignalSlotlException with error message $message.
     *
     * @param string $message
     */
    public function __construct( $message )
    {
        parent::__construct( $message );
    }
}
?>
