<?php
/**
 * File containing the ezcConsoleDialogValidator interface.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface that every console dialog validator class must implement.
 *
 * @package ConsoleTools
 * @version 1.6.1
 */
interface ezcConsoleDialogValidator
{
    /**
     * Perform no conversion on the result. 
     */
    const CONVERT_NONE  = 0;

    /**
     * Convert result to lower-case. 
     */
    const CONVERT_LOWER = 1;

    /**
     * Convert result to upper-case. 
     */
    const CONVERT_UPPER = 2;

    /**
     * Returns if the given result is valid. 
     * 
     * @param mixed $result The received result.
     * @return bool If the result is valid.
     */
    public function validate( $result );

    /**
     * Returns a fixed version of the result, if possible.
     * This method tries to repair the submitted result, if it is not valid,
     * yet. Fixing can be done in different ways, like casting into a certain
     * datatype, string manipulation, creating an object. A result returned
     * by fixup must not necessarily be valid, so a dialog should call validate
     * after trying to fix the result.
     * 
     * @param mixed $result The received result.
     * @return mixed The manipulated result.
     */
    public function fixup( $result );

}

?>
