<?php
/**
 * File containing the ezcTreeDbInvalidSchemaException
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.1
 * @filesource
 * @package Tree
 */

/**
 * Exception that is thrown when an incompatible schema is detected with
 * one of the Tree operations.
 *
 * @package Tree
 * @version 1.1.1
 */
class ezcTreeDbInvalidSchemaException extends ezcTreeException
{
    /**
     * Constructs a new ezcTreeDbInvalidSchemaException
     *
     * @param string $operation
     * @param string $message
     */
    public function __construct( $operation, $message )
    {
        parent::__construct( "While {$operation}, an incompatible schema was found: {$message}." );
    }
}
?>
