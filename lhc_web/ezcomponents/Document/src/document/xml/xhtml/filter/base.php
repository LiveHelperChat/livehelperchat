<?php
/**
 * File containing the ezcDocumentXhtmlBaseFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Abstract base class for XHtml filters, assigning semantic information to
 * XHtml documents.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
abstract class ezcDocumentXhtmlBaseFilter
{
    /**
     * Filter XHtml document
     *
     * Filter for the document, which may modify / restructure a document and
     * assign semantic information bits to the elements in the tree.
     *
     * @param DOMDocument $document
     * @return DOMDocument
     */
    abstract public function filter( DOMDocument $document );
}

?>
