<?php
/**
 * File containing the ezcQueryInvalidParameterException class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown when a method does not the receive correct variables it requires.
 *
 * @package Database
 * @version 1.4.7
 */
class ezcQueryInvalidParameterException extends ezcQueryException
{
    /**
     * Constructs an ezcQueryVariableParameterException.
     *
     * @param string $method
     * @param int $parameterNumber
     * @param string $foundContents
     * @param string $expectedContents
     */
    public function __construct( $method, $parameterNumber, $foundContents, $expectedContents )
    {
        $info = "Argument '{$parameterNumber}' of method '{$method}' expects {$expectedContents} but {$foundContents} was provided.";
        parent::__construct( $info );
    }
}
?>
