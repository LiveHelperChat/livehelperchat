<?php
/**
 * File containing the ezcTreeTransactionAlreadyStartedException class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * Exception that is thrown when a transaction is active and
 * "beginTransaction()" is called again.
 *
 * @package Tree
 * @version 1.1.4
 */
class ezcTreeTransactionAlreadyStartedException extends ezcTreeException
{
    /**
     * Constructs a new ezcTreeTransactionAlreadyStartedException.
     */
    public function __construct()
    {
        parent::__construct( "A transaction has already been started." );
    }
}
?>
