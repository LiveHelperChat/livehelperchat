<?php
/**
 * File containing the ezcDocumentRstMissingTextRoleHandlerException class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown, when a RST contains a text role, for which no handler has
 * been registered.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstMissingTextRoleHandlerException extends ezcDocumentException
{
    /**
     * Construct exception from text role name
     *
     * @param string $name
     */
    public function __construct( $name )
    {
        parent::__construct(
            "No text role handler registered for text role '{$name}'."
        );
    }
}

?>
