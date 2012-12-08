<?php
/**
 * File containing the ezcConsoleMenuDialogValidator interface.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface that every console menu dialog validator class must implement.
 *
 * @package ConsoleTools
 * @version 1.6.1
 */
interface ezcConsoleMenuDialogValidator extends ezcConsoleQuestionDialogValidator
{

    /**
     * Returns an array of the elements to display. 
     * 
     * @return array(string=>string) Elements to display.
     */
    public function getElements();

}

?>
