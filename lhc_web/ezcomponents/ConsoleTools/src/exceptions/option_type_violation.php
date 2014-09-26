<?php
/**
 * File containing the ezcConsoleOptionTypeViolationException.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An option was submitted with an illigal type.
 * This exception can be caught using {@link ezcConsoleOptionException}.
 *
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleOptionTypeViolationException extends ezcConsoleOptionException
{
    /**
     * Creates a new exception object. 
     * 
     * @param ezcConsoleOption $option The option affected by the violation.
     * @param mixed $value             The violating value            The violating value..
     * @return void
     */
    public function __construct( ezcConsoleOption $option, $value )
    {
        $typeName = 'unknown';
        switch ( $option->type )
        {
            case ezcConsoleInput::TYPE_NONE:
                $typeName = 'none';
                break;
            case ezcConsoleInput::TYPE_INT:
                $typeName = 'int';
                break;
        }
        parent::__construct( "The option '{$option->long}' expects a value of type '{$typeName}', but received the value '{$value}'." );
    }
}
?>
