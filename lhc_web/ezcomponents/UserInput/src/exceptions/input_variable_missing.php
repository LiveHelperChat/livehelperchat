<?php
/**
 * @package UserInput
 * @version 1.4
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown when a required input field is missing.
 *
 * @package UserInput
 * @version 1.4
 */
class ezcInputFormVariableMissingException extends ezcInputFormException
{
    /**
     * Constructs a new ezcInputFormVariableMissingException.
     *
     * @param string $fieldName
     * @return void
     */
    function __construct( $fieldName )
    {
        parent::__construct( "Required input field '{$fieldName}' missing." );
    }
}
?>
