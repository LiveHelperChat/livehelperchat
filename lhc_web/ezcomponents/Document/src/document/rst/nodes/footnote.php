<?php
/**
 * File containing the ezcDocumentRstFootnoteNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The footnote AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstFootnoteNode extends ezcDocumentRstNode
{
    /**
     * Footnote target name
     *
     * @var array
     */
    public $name;

    /**
     * Footnote number
     *
     * @var int
     */
    public $number;

    /**
     * Type of footnote. May be either a normal footnote, or a citation
     * reference.
     *
     * @var int
     */
    public $footnoteType = self::NUMBERED;

    /**
     * Numbered footnote
     */
    const NUMBERED = 1;

    /**
     * Auto numbered footnote
     */
    const AUTO_NUMBERED = 2;

    /**
     * Labeled auto numbered footnote
     */
    const LABELED = 4;

    /**
     * Footnote using symbols
     */
    const SYMBOL = 8;

    /**
     * Footnote is citation reference
     */
    const CITATION = 16;

    /**
     * Construct RST document node
     *
     * @param ezcDocumentRstToken $token
     * @param array $name
     * @param int $footnoteType
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token, array $name, $footnoteType = self::NUMBERED )
    {
        // Perhaps check, that only node of type section and metadata are
        // added.
        parent::__construct( $token, self::FOOTNOTE );
        $this->name = $name;

        $this->footnoteType = $footnoteType;
    }

    /**
     * Set state after var_export
     *
     * @param array $properties
     * @return void
     * @ignore
     */
    public static function __set_state( $properties )
    {
        $node = new ezcDocumentRstFootnoteNode(
            $properties['token'],
            $properties['name']
        );

        $node->nodes        = $properties['nodes'];
        $node->footnoteType = $properties['footnoteType'];
        return $node;
    }
}

?>
