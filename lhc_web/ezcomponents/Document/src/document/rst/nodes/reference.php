<?php
/**
 * File containing the ezcDocumentRstReferenceNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The internal footnote reference AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstReferenceNode extends ezcDocumentRstLinkNode
{
    /**
     * Type of footnote. May be either a normal footnote, or a citation
     * reference.
     *
     * @var int
     */
    public $footnoteType = ezcDocumentRstFootnoteNode::NUMBERED;

    /**
     * Tokens containing the footnote name
     *
     * @var array
     */
    public $name = array();

    /**
     * Construct RST document node
     *
     * @param ezcDocumentRstToken $token
     * @param int $footnoteType
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token, $footnoteType = ezcDocumentRstFootnoteNode::NUMBERED )
    {
        parent::__construct( $token, self::REFERENCE );
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
        $node = new ezcDocumentRstReferenceNode(
            $properties['token']
        );

        $node->name         = $properties['name'];
        $node->nodes        = $properties['nodes'];
        $node->footnoteType = $properties['footnoteType'];
        return $node;
    }
}

?>
