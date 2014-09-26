<?php
/**
 * @package UserInput
 * @version 1.4
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown when an invalid input source is used.
 *
 * @package UserInput
 * @version 1.4
 */
class ezcInputFormWrongInputSourceException extends ezcInputFormException
{
    /**
     * Constructs a new ezcInputFormWrongInputSourceException.
     *
     * @param string $inputSource
     * @return void
     */
    function __construct( $inputSource )
    {
        parent::__construct( "Wrong input source '{$inputSource}'." );
    }
}
?>
