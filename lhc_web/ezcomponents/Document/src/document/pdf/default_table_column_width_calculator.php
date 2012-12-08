<?php
/**
 * File containing the ezcDocumentPdfDefaultTableColumnWidthCalculator class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Table column width calculator
 *
 * Default implementation for a table column width calculator, which is 
 * responsible to estimate / guess / calculate sensible column width for a 
 * docbook table definition.
 *
 * Introspects the contents of a table and guesses based on included media and 
 * number of words in a cell what a reasonable column width might be.
 *
 * Since this implementation is mostly based on the count and length of words 
 * in one column, it might return unreasonably small column sizes for single 
 * columns. This might lead to columns, where not even a single characters fits 
 * in, which may cause problems while rendering.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentPdfDefaultTableColumnWidthCalculator extends ezcDocumentPdfTableColumnWidthCalculator
{
    /**
     * Estimate column widths
     *
     * Should return an array with the column widths given as float numbers 
     * between 0 and 1, which all add together to 1.
     * 
     * @param DOMElement $table 
     * @return array
     */
    public function estimateWidths( DOMElement $table )
    {
        $xpath = new DOMXPath( $table->ownerDocument );
        $columns = array();
        foreach ( $xpath->query( './*/*/*[local-name() = "row"] | ./*/*[local-name() = "row"]', $table ) as $rowNr => $row )
        {
            foreach ( $xpath->query( './*[local-name() = "entry"]', $row ) as $cellNr => $cell )
            {
                $columns[$cellNr][$rowNr]['text']  = trim( strip_tags( $cell->textContent ) );
                $columns[$cellNr][$rowNr]['media'] = $cell->getElementsByTagName( 'mediaobject' );
            }
        }

        // Calculate guess values for amount of text in cells
        $textFactors = array_fill( 0, count( $columns ), 0. );
        foreach ( $columns as $nr => $column )
        {
            foreach ( $column as $cell )
            {
                $words = preg_split( '(\s+)', $cell['text'] );
                $count = count( $words );
                array_map( 'strlen', $words );

                $textFactors[$nr] += $count + max( $words ) / $count;
            }
        }

        // Normalize values
        $sum = array_sum( $textFactors );
        foreach ( $textFactors as $nr => $factor )
        {
            $textFactors[$nr] /= $sum;
        }

        $textFactors = array_map( 'sqrt', $textFactors );

        // Normalize values
        $sum = array_sum( $textFactors );
        foreach ( $textFactors as $nr => $factor )
        {
            $textFactors[$nr] /= $sum;
        }

        return $textFactors;
    }
}
?>
