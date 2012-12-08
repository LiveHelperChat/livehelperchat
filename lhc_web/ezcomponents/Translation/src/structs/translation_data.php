<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * A container to store one translatable string.
 *
 * This struct is used in various classes to store the data accompanying one
 * translatable string.
 *
 * @package Translation
 * @version 1.3.2
 */
class ezcTranslationData extends ezcBaseStruct
{
    /**
     * Used when the translated string is up-to-date
     */
    const TRANSLATED = 0;

    /**
     * Used when a translated string has not been translated yet.
     */
    const UNFINISHED = 1;

    /**
     * Used when a translated string is obsolete.
     */
    const OBSOLETE = 2;

    /**
     * The original untranslated source string.
     *
     * @var string
     */
    public $original;

    /**
     * The translated string.
     *
     * @var string
     */
    public $translation;

    /**
     * Comment about the translation.
     *
     * @var string
     */
    public $comment;

    /**
     * The status, which is one of the three constants TRANSLATED, UNFINISHED or OBSOLETE.
     *
     * @var integer
     */
    public $status;

    /**
     * The filename the string was found in
     *
     * @var string
     */
    public $filename;

    /**
     * The line where the string is
     *
     * @var integer
     */
    public $line;

    /**
     * The column where the string is
     *
     * @var integer
     */
    public $column;

    /**
     * Constructs an ezcTranslationData object.
     *
     * @param string $original
     * @param string $translation
     * @param string $comment
     * @param int $status
     * @param string $filename
     * @param int $line
     * @param int $column
     */
    function __construct( $original, $translation, $comment, $status, $filename = null, $line = null, $column = null )
    {
        $this->original = $original;
        $this->translation = $translation;
        $this->comment = $comment;
        $this->status = $status;
        $this->filename = $filename;
        $this->line = $line;
        $this->column = $column;
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
     * @return ezcTranslationData
     * @ignore
     */
    static public function __set_state( array $array )
    {
        return new ezcTranslationData( $array['original'], $array['translation'], $array['comment'], $array['status'], $array['filename'], $array['line'], $array['column'] );
    }
}
?>
