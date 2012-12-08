<?php
/**
 * File containing the ezcArchiveValueException class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown when encountering a wrong archive value.
 *
 * @package Archive
 * @version 1.4.1
 */
class ezcArchiveValueException extends ezcArchiveException
{
    /**
     * Construct an archive exception.
     *
     * If $expectedValue is provided then it will be included in the exception
     * message thrown.
     *
     * @param mixed $value
     * @param mixed $expectedValue
     */
    public function __construct( $value, $expectedValue = null )
    {
        $type = gettype( $value );
        if ( in_array( $type, array( 'array', 'object', 'resource' ) ) )
        {
            $value = serialize( $value );
        }

        $msg = "The value '{$value}' is incorrect.";
        if ( $expectedValue )
        {
            $msg .= " Allowed values are: " . $expectedValue . '.';
        }
        parent::__construct( $msg );
    }
}
?>
