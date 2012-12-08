<?php
/**
 * File containing the abstrac ezcDocumentDocbookToHtmlBaseHandler base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Basic converter which stores a list of handlers for each node in the docbook
 * element tree. Those handlers will be executed for the elements, when found.
 * The handler can then handle the repective subtree.
 *
 * Additional handlers may be added by the user to the converter class.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentDocbookToHtmlBaseHandler extends ezcDocumentElementVisitorHandler
{
    /**
     * Reference to HTML head element
     *
     * @var DOMElement
     */
    private $head = null;

    /**
     * Get head of HTML document
     *
     * Get the root node of the HTML document head
     *
     * @param DOMElement $element
     * @return DOMElement
     */
    protected function getHead( DOMElement $element )
    {
        if ( $this->head === null )
        {
            // Get reference to head node in destination document
            $xpath = new DOMXPath( $element->ownerDocument );
            $this->head = $xpath->query( '/*[local-name() = "html"]/*[local-name() = "head"]' )->item( 0 );
        }

        return $this->head;
    }
}

?>
