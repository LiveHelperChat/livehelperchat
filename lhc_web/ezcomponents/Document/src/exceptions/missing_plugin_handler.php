<?php
/**
 * Class containing the ezcDocumentWikiMissingPluginHandlerException class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown, when a wiki contains a plugin, for which no handler has
 * been registerd.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiMissingPluginHandlerException extends ezcDocumentException
{
    /**
     * Construct exception from directive name
     *
     * @param string $name
     */
    public function __construct( $name )
    {
        parent::__construct(
            "No plugin handler registered for plugin '{$name}'."
        );
    }
}

?>
