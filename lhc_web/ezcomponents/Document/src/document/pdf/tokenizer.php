<?php
/**
 * File containing the ezcDocumentPdfTokenizer class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract base class for tokenizer implementations.
 *
 * Tokenizers are used to split a series of words (sentences) into single
 * words, which can be rendered split by spaces.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentPdfTokenizer
{
    /**
     * Constant indicating a breaking point, including a rendered space.
     */
    const SPACE = 0;

    /**
     * Constant indicating a possible breaking point without rendering a space
     * character.
     */
    const WRAP = 1;

    /**
     * Constant indicating a forced breaking point without rendering a space
     * character.
     */
    const FORCED = 2;

    /**
     * Split string into words
     *
     * This function takes a string and splits it into words. There are
     * different mechanisms which indicate possible splitting points in the
     * resulting word stream:
     *
     * - self:SPACE: The renderer might render a space
     * - self:WRAP: The renderer might wrap the line at this position, but will
     *   not render spaces, might as well just be omitted.
     *
     * A possible splitting of an english sentence might look like:
     *
     * <code>
     *  array(
     *      'Hello',
     *      self:SPACE,
     *      'world!',
     *  );
     * </code>
     *
     * Non breaking spaces should not be splitted into multiple words, so there
     * will be no break applied.
     *
     * @param string $string
     * @return array
     */
    abstract public function tokenize( $string );
}

?>
