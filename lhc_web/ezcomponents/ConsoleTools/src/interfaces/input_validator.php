<?php
/**
 * File containing the ezcConsoleInputValidator interface.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */
/**
 * Interface for input validators used in ezcConsoleInput.
 *
 * An instance of this interface is used in {@link ezcConsoleInput} to validate 
 * options and arguments.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 *
 * @access private
 * @TODO Verify interface and make it public to replace the validation in 
 *       {@link ezcConsoleInput}.
 */
interface ezcConsoleInputValidator
{
    /**
     * Validates the given options.
     *
     * May throw an exception that derives from {@link ezcConsoleException}.  
     * Receives the array of $options defined for validation and $hasArguments 
     * to indicates if arguments have been submitted in addition.
     *
     * @param array(ezcConsoleOption) $options
     * @param bool $hasArguments
     */
    public function validateOptions( array $options, $hasArguments );

    // @TODO: validateArguments();
}

?>
