<?php
/**
 * File containing the ezcPhpGeneratorFlowException class
 *
 * @package PhpGenerator
 * @version 1.0.6
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Flow exceptions are thrown when control structures like if and while are closed out of order.
 *
 * @package PhpGenerator
 * @version 1.0.6
 */
class ezcPhpGeneratorFlowException extends ezcPhpGeneratorException
{
    /**
     * Constructs a new flow exception.
     *
     * $expectedFlow is the name of the control structure you expected the end of
     * and $calledFlow is the actual structure received.
     *
     * @param string $expectedFlow
     * @param string $calledFlow
     */
    function __construct( $expectedFlow, $calledFlow )
    {
        parent::__construct( "Expected end of '{$expectedFlow}' but got end of '{$calledFlow}'" );
    }
}

?>
