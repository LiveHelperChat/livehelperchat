<?php
/**
 * File containing the ezcConsoleArgumentTypeViolationException class.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An argument was submitted with an illigal type.
 * This exception can be caught using {@link ezcConsoleArgumentException}.
 *
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleArgumentTypeViolationException extends ezcConsoleArgumentException
{
    /**
     * Creates a new exception object. 
     * 
     * @param ezcConsoleArgument $arg The violated argument.
     * @param mixed $value            The incorrect value.
     * @return void
     */
    public function __construct( ezcConsoleArgument $arg, $value )
    {
        $typeName = 'unknown';
        switch ( $arg->type )
        {
            case ezcConsoleInput::TYPE_INT:
                $typeName = 'int';
                break;
        }
        parent::__construct( "The argument '{$arg->name}' expects a value of type '{$typeName}', but received the value '{$value}'." );
    }
}

?>
