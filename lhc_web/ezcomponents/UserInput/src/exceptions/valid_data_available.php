<?php
/**
 * @package UserInput
 * @version 1.4
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is used when valid data is available and you try to access RAW data.
 *
 * @package UserInput
 * @version 1.4
 */
class ezcInputFormValidDataAvailableException extends ezcInputFormException
{
    /**
     * Constructs a new ezcInputFormValidDataAvailableException.
     *
     * @param string $fieldName
     * @return void
     */
    function __construct( $fieldName )
    {
        parent::__construct( "You are not allowed to request RAW data for the '{$fieldName}' field which has valid data." );
    }
}
?>
