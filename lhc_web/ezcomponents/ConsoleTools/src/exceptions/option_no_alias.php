<?php
/**
 * File containing the ezcConsoleOptionNoAliasException.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Occurs if the alias you tried to unregister is not an alias, but a real option.
 *
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleOptionNoAliasException extends ezcConsoleException
{
    /**
     * Creates a new exception object. 
     * 
     * @param string $name The name of the option which is not an alias.
     * @return void
     */
    public function __construct( $name )
    {
        parent::__construct( "The option name '{$name}' refers to a real parameter, not to an alias." );
    }
}

?>
