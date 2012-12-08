<?php
/**
 * File containing the abstract ezcDocumentElementVisitorHandler base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract base handler class for conversions done by converters extending
 * from ezcDocumentDocbookElementVisitorConverter.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentElementVisitorHandler
{
    /**
     * Handle a node.
     *
     * Handle / transform a given node, and return the result of the
     * conversion.
     *
     * @param ezcDocumentElementVisitorConverter $converter 
     * @param DOMElement $node 
     * @param mixed $root 
     * @return mixed
     */
    abstract public function handle( ezcDocumentElementVisitorConverter $converter, DOMElement $node, $root );
}

?>
