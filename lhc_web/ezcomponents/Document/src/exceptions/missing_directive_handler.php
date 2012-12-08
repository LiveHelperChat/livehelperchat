<?php
/**
 * File containing the ezcDocumentRstMissingDirectiveHandlerException class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown, when a RST contains a directive, for which no handler has
 * been registerd.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstMissingDirectiveHandlerException extends ezcDocumentException
{
    /**
     * Construct exception from directive name
     *
     * @param string $name
     * @return void
     */
    public function __construct( $name )
    {
        parent::__construct(
            "No directive handler registered for directive '{$name}'."
        );
    }
}

?>
