<?php
/**
 * File containing the ezcSearchResultDocument class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The struct contains the result as parsed by the different search handlers.
 *
 * @package Search
 * @version 1.0.9
 * @mainclass
 */
class ezcSearchResultDocument
{
    /**
     * Document score
     *
     * @var float
     */
    public $score;

    /**
     * Document itself
     *
     * @var object(mixed)
     */
    public $document;

    /**
     * The highlighted fields
     *
     * The index is the field name, and the value the highlighted value.
     *
     * @var array(string=>string)
     */
    public $highlight;

    /**
     * Contructs a new ezcSearchResultDocument.
     *
     * @param float $score
     * @param object(mixed) $document
     * @param array(string=>string) $highlight
     */
    public function __construct( $score = 0, $document = null, $highlight = array() )
    {
        $this->score = $score;
        $this->document = $document;
        $this->highlight = $highlight;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcSearchResult
     */
    static public function __set_state( array $array )
    {
        return new ezcSearchResultDocument(
            $array['score'], $array['document'], $array['highlight']
        );
    }
}
?>
