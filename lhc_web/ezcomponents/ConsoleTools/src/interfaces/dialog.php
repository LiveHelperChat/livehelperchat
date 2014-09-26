<?php
/**
 * File containing the ezcConsoleDialog interface.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface that every console dialog class must implement.
 *
 * Console dialogs can either be used on their own or using the
 * {@link ezcConsoleDialogViewer} (recommended). In the dialog viewer, a dialog
 * is instanciated and displayed in a loop, until it receives a valid result
 * value.
 *
 * @package ConsoleTools
 * @version 1.6.1
 */
interface ezcConsoleDialog
{

    /**
     * Create a new dialog object.
     * This method retrieves an ezcConsoleOutput object for printing its
     * content. Additionally an instance of ezcConsoleDialogOptions or a derived
     * class is received to configure the behaviour of the dialog. Dialog
     * implementations may extend ezcConsoleDialogOptions and require an
     * instance of their extended class as options.
     * 
     * @param ezcConsoleOutput $output         Output object.
     * @param ezcConsoleDialogOptions $options Options.
     * @return void
     */
    // public function __construct( ezcConsoleOutput $output, ezcConsoleDialogOptions $options = null );

    /**
     * Returns if the dialog retrieved a valid result.
     * Typically a dialog is displayed in a loop until he received a valid
     * result. What a valid result is, is determined by the dialog itself.
     * 
     * @return bool If a valid result was retrieved.
     */
    public function hasValidResult();

    /**
     * Returns the result retrieved.
     * If no valid result was retreived, yet, this method should throw an
     * {@link ezcDialogNoValidResultException}. Otherwise this method returns
     * the result.
     * 
     * @return mixed The retreived result.
     *
     * @throws ezcDialogNoValidResultException
     *         if this method is called without a valid result being retrieved
     *         by the object. Use {@link hasValidResult()} to avoid this
     *         exception.
     */
    public function getResult();

    /**
     * Displays the dialog.
     * Displays the dialog. Dialogs will most propably block the application until
     * the user took some interaction.
     * 
     * @return void
     */
    public function display();

    /**
     * Resets the dialog to its initial state. 
     * Resets the dialog to its orginal state in respect to its internal
     * changes. Note: Any changes you made to the options are kept in tact.
     * 
     * @return void
     */
    public function reset();
}

?>
