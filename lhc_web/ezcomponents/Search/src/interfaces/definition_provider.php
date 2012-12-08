<?php
/**
 * File containing the ezcSearchDefinitionProvider class
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Defines the interface for all classes that can provide a definition through the ezcSearchEmbeddedManager.
 *
 * @version 1.0.9
 * @package Search
 */
interface ezcSearchDefinitionProvider
{
    /**
     * Returns the definition for the document.
     *
     * @throws ezcSearchDefinitionNotFoundException if no such definition can be found.
     * @return ezcSearchDocumentDefinition
     */
    static public function getDefinition();
}
?>
