<?php
/**
 * File containing the ezcDocumentDocbookToWikiTableHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit tables.
 *
 * The RST table rendering algorithm tries losely to fit a table in the
 * provided document dimensions. This may not always work for over long words,
 * or if the table cells contain literal blocks which can not be wrapped.
 *
 * For this the algorithm estiamtes the available width per column, equally
 * distributes this available width over all columns (which might be far from
 * optimal), and extends the total table width if some cell content exceeds the
 * column width.
 *
 * The initial table cell estiation happens inside the function
 * estimateColumnWidths() which you might want to extend to fit your needs
 * better.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToWikiTableHandler extends ezcDocumentDocbookToWikiBaseHandler
{
    /**
     * Handle a node
     *
     * Handle / transform a given node, and return the result of the
     * conversion.
     *
     * @param ezcDocumentElementVisitorConverter $converter
     * @param DOMElement $node
     * @param mixed $root
     * @return mixed
     */
    public function handle( ezcDocumentElementVisitorConverter $converter, DOMElement $node, $root )
    {
        $rows = $node->getElementsByTagName( 'row' );

        foreach ( $rows as $row )
        {
            $header = ( $row->parentNode->tagName === 'thead' );
            $cells = $row->getElementsByTagName( 'entry' );
            foreach ( $cells as $cell )
            {
                $root .= ( $header ? '|= ' : '| ' ) .
                    preg_replace( '(\s+)', ' ', trim( $converter->visitChildren( $cell, '' ) ) );
                $root .= ' ';
            }
            $root .= "\n";
        }

        $root .= "\n";
        return $root;
    }
}

?>
