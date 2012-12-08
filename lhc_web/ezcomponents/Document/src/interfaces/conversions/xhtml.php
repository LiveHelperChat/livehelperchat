<?php
/**
 * File containing the ezcDocumentXhtmlConversion interface.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * An interface indicating the ability to transform a document directly into
 * XHTML.
 *
 * @package Document
 * @version 1.3.1
 */
interface ezcDocumentXhtmlConversion
{
    /**
     * Return document compiled to the XHTML format
     *
     * The internal document structure is compiled to the XHTML format and the
     * resulting XHTML document is returned.
     *
     * This is an optional interface for document markup languages which
     * support a direct transformation to XHTML as a shortcut.
     *
     * @return ezcDocumentXhtml
     */
    public function getAsXhtml();
}

?>
