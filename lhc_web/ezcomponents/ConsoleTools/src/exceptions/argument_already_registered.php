<?php
/**
 * File containing the ezcConsoleArgumentAlreadyRegisteredException class.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * There is already an argument registered with the given name or at the given place.
 *
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleArgumentAlreadyRegisteredException extends ezcConsoleException
{
    /**
     * The name of the argument is already in use.
     */
    const NAMED = 1;
    
    /**
     * The position of the argument is already in use. Unset the position first and the re-register.
     */
    const ORDERED = 2;

    /**
     * Creates a new exception object.
     * The $type parameter can either be
     * {@link ezcConsoleArgumentAlreadyRegisteredException::NAMED} or
     * {@link ezcConsoleArgumentAlreadyRegisteredException::ORDERED}, indicating
     * if the name of the parameter or its place are already taken.
     * 
     * @param int $offset Offset of the already reagistered argument.
     * @param int $type   Type of the offset.
     * @return void
     */
    public function __construct( $offset, $type )
    {
        switch ( $type )
        {
            case self::NAMED:
                $message = "Argument with name '$offset' already registered.";
                break;
            case self::ORDERED:
                $message = "Argument at position '$offset' already registered.";
                break;
        }
        parent::__construct( $message );
    }
}
?>
