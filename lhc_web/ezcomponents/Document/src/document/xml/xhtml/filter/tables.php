<?php
/**
 * File containing the ezcDocumentXhtmlTablesFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter, which tries to filter out tables, which do not have typical table
 * contents. Eg. are used for layout instead of content markup.
 *
 * The filter checks the number of cells which contain mostly text and when the
 * factor drops below a configured threshold the table is removed from the
 * content tree.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentXhtmlTablesFilter extends ezcDocumentXhtmlBaseFilter
{
    /**
     * Percent of cells which are required to contain textual content.
     *
     * @var float
     */
    protected $threshold = .8;

    /**
     * Construct tables filter
     *
     * Construct the tables filter with the percentage values of cells with
     * textual contents requierd for each table not to be deleted. It defaults
     * to .8 (80%).
     *
     * @param float $threshold
     * @return void
     */
    public function __construct( $threshold = .8 )
    {
        $this->threshold = (float) $threshold;
    }

    /**
     * Filter XHtml document
     *
     * Filter for the document, which may modify / restructure a document and
     * assign semantic information bits to the elements in the tree.
     *
     * @param DOMDocument $document
     * @return DOMDocument
     */
    public function filter( DOMDocument $document )
    {
        $xpath = new DOMXPath( $document );

        // Find all tables
        $tables = $xpath->query( '//*[local-name() = "table"]' );

        foreach ( $tables as $table )
        {
            // Ignore tables, which again contain tables, as these most
            // probably contain the website content somehow.
            if ( $xpath->query( './/*[local-name() = "table"]', $table )->length > 0 )
            {
                continue;
            }

            // Extract all cells from the table and check what they contain
            $cells = $xpath->query( './/*[local-name() = "td"] | .//*[local-name() = "th"]', $table );
            $cellCount = $cells->length;
            $cellContentCount = 0;

            foreach ( $cells as $cell )
            {
                $cellContentCount += (int) $this->cellHasContent( $cell );
            }

            // Completely remove table, if it does not meet the configured
            // expectations
            if ( ( $cellContentCount / $cellCount ) < $this->threshold )
            {
                $table->parentNode->removeChild( $table );
                continue;
            }

            // Tables with only one column are most probably also used only for
            // layout. We remove them, too.
            if ( $xpath->query( './/*[local-name() = "tr"]', $table )->length >= $cellCount )
            {
                $table->parentNode->removeChild( $table );
                continue;
            }
        }
    }

    /**
     * Check if table has proper content
     *
     * Retrun true, if the cell has proper textual content.
     *
     * Extensions of this method may check for patterns in the table contents
     * for better detection of the table semantics.
     *
     * @param DOMElement $cell
     * @return bool
     */
    protected function cellHasContent( DOMElement $cell )
    {
        return (bool) strlen( trim( $cell->textContent ) );
    }
}

?>
