<?php
/**
 * File containing the ezcDocumentPdfHyphenator class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Abstract base class for hyphenation implementations.
 *
 * Hyphenation implementations are responsbile for language dependant splitting
 * of words into hyphens, for better text wrapping especially in justified
 * paragraphs.
 *
 * A proper hyphenation implementation should most probably be based on 
 * dicstionary files, provided by external tools.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
abstract class ezcDocumentPdfHyphenator
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
    abstract public function splitWord( $word );
}
?>
