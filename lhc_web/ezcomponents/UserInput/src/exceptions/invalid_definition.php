<?php
/**
 * @package UserInput
 * @version 1.4
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown when an invalid definition array is used.
 *
 * @package UserInput
 * @version 1.4
 */
class ezcInputFormInvalidDefinitionException extends ezcInputFormException
{
    /**
     * Constructs a new ezcInputFormInvalidDefinitionException.
     *
     * @param string $validationMessage
     * @return void
     */
    function __construct( $validationMessage )
    {
        parent::__construct( "Invalid definition array: {$validationMessage}." );
    }
}
?>
