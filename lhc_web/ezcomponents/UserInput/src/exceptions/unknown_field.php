<?php
/**
 * @package UserInput
 * @version 1.4
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown when a fieldname is used that was not defined in a definition array.
 *
 * @package UserInput
 * @version 1.4
 */
class ezcInputFormUnknownFieldException extends ezcInputFormException
{
    /**
     * Constructs a new ezcInputFormUnknownFieldException.
     *
     * @param string $fieldName
     * @return void
     */
    function __construct( $fieldName )
    {
        parent::__construct( "The field '{$fieldName}' is not defined." );
    }
}
?>
