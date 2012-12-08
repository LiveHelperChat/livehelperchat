<?php
/**
 * File containing the ezcDocumentPdfDefaultHyphenator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Default hyphenation implementation, which does no word splitting at all.
 *
 * Because no splitting is applied at all, it should work for all languages, 
 * but might produce bad results for short lines. Extend the base class and 
 * register your own hyphenator for better results.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfDefaultHyphenator extends ezcDocumentPdfHyphenator
{
    /**
     * Split word into hypens
     *
     * Takes a word as a string and should return an array containing arrays of
     * two words, which each represent a possible split of a word. The german
     * word "Zuckerstück" for example changes its hyphens depending on the
     * splitting point, so the return value would look like:
     *
     * <code>
     *  array(
     *      array( 'Zuk-', 'kerstück' ),
     *      array( 'Zucker-', 'stück' ),
     *  )
     * </code>
     *
     * You should always also include the concatenation character in the split
     * words, since it might change depending on the used language.
     *
     * @param mixed $word
     * @return void
     */
    public function splitWord( $word )
    {
        return array( array( $word ) );
    }
}
?>
