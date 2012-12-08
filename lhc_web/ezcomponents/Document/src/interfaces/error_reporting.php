<?php
/**
 * File containing the ezcDocumentErrorReporting interface.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for error reporting.
 *
 * @package Document
 * @version 1.3.1
 */
interface ezcDocumentErrorReporting
{
    /**
     * Trigger parser error.
     *
     * Emit a parser error and handle it dependiing on the current error
     * reporting settings.
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param int $position
     */
    public function triggerError( $level, $message, $file = null, $line = null, $position = null );

    /**
     * Return list of errors occured during visiting the document.
     *
     * May be an empty array, if on errors occured, or a list of
     * ezcDocumentVisitException objects.
     *
     * @return array
     */
    public function getErrors();
}

?>
