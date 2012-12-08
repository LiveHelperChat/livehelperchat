<?php
/**
 * File containing the ezcPhpGeneratorException class
 *
 * @package PhpGenerator
 * @version 1.0.6
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * General exception class for the PhpGenerator package.
 *
 * @package PhpGenerator
 * @version 1.0.6
 */
class ezcPhpGeneratorException extends ezcBaseException
{
    /**
     * Constructs a new ezcPhpGeneratorException with error message $message.
     *
     * @param string $message
     * @return void
     */
    public function __construct( $message )
    {
        parent::__construct( $message );
    }
}
?>
