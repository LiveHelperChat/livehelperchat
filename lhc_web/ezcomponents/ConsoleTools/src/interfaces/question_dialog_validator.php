<?php
/**
 * File containing the ezcConsoleQuestionDialogValidator interface.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface that every console question dialog validator class must implement.
 *
 * @package ConsoleTools
 * @version 1.6.1
 */
interface ezcConsoleQuestionDialogValidator extends ezcConsoleDialogValidator
{

    /**
     * Returns a string of possible results to be displayed with the question. 
     * For example "(y/n) [y]" to indicate "y" and "n" are valid values and "y" is
     * preselected.
     *
     * @return string The result string.
     */
    public function getResultString();
}

?>
