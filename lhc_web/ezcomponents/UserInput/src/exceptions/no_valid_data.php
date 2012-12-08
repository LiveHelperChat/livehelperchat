<?php
/**
 * @package UserInput
 * @version 1.4
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown when an invalid field name is requested.
 *
 * @package UserInput
 * @version 1.4
 */
class ezcInputFormNoValidDataException extends ezcInputFormException
{
    /**
     * Constructs a new ezcInputFormNoValidDataException.
     *
     * @param string $fieldName
     * @return void
     */
    function __construct( $fieldName )
    {
        parent::__construct( "Invalid field name '{$fieldName}' requested." );
    }
}
?>
