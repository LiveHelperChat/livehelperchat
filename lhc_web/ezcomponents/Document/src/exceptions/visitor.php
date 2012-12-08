<?php
/**
 * File containing the ezcDocumentVisitException class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown, when the RST visitor could not visit an AST node
 * properly.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentVisitException extends ezcDocumentException
{
    /**
     * Construct exception from errnous string and current position
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param int $position
     * @return void
     */
    public function __construct( $level, $message, $file, $line, $position )
    {
        $levelMapping = array(
            E_NOTICE  => 'Notice',
            E_WARNING => 'Warning',
            E_ERROR   => 'Error',
            E_PARSE   => 'Fatal error',
        );

        parent::__construct(
            sprintf( "Visitor error: %s: '%s' in line %d at position %d.",
                $levelMapping[$level],
                $message,
                $line,
                $position
            )
        );
    }
}

?>
