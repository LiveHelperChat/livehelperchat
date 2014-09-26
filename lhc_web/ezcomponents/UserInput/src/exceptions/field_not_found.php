<?php
/**
 * @package UserInput
 * @version 1.4
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown when a specified field is not found
 *
 * @package UserInput
 * @version 1.4
 */
class ezcInputFormFieldNotFoundException extends ezcInputFormException
{
    /**
     * Constructs a new ezcInputFormFieldNotFoundException.
     *
     * @param string $fieldName
     * @return void
     */
    function __construct( $fieldName )
    {
        parent::__construct( "The field '{$fieldName}' could not be found in the input source." );
    }
}
?>
