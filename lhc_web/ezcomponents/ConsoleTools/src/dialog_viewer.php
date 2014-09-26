<?php
/**
 * File containing the ezcConsoleDialogViewer class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Utility class for ezcConsoleDialog implementations.
 * This class contains utility methods for working with {@link
 * ezcConsoleDialog} implementations.
 *
 * To display a dialog in a loop until a valid result is received do:
 * <code>
 * // Instatiate dialog in $dialog ...
 * ezcConsoleDialogViewer::displayDialog( $dialog );
 * </code>
 *
 * For implementing a custom dialog, the method {@link readLine()} method can be
 * used to read a line of input from the user.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleDialogViewer
{
    /**
     * Displays a dialog and returns a valid result from it.
     * This methods displays a dialog in a loop, until it received a valid
     * result from it and returns this result.
     * 
     * @param ezcConsoleDialog $dialog The dialog to display.
     * @return mixed The result from this dialog.
     */
    public static function displayDialog( ezcConsoleDialog $dialog )
    {
        do
        {
            $dialog->display();
        }
        while ( $dialog->hasValidResult() === false );
        return $dialog->getResult();
    }

    /**
     * Returns a line from STDIN.
     * The returned line is fully trimmed.
     * 
     * @return string
     * @throws ezcConsoleDialogAbortException
     *         if the user closes STDIN using <CTRL>-D.
     */
    public static function readLine()
    {
        $res = trim( fgets( STDIN ) );
        if ( feof( STDIN ) )
        {
            throw new ezcConsoleDialogAbortException();
        }
        return $res;
    }
}

?>
