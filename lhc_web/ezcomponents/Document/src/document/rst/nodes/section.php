<?php
/**
 * File containing the ezcDocumentRstSectionNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The document section AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstSectionNode extends ezcDocumentRstNode
{
    /**
     * Section title
     *
     * @var string
     */
    public $title;

    /**
     * Depth of section nesting
     *
     * @var int
     */
    public $depth;

    /**
     * Title reference name
     *
     * @var string
     */
    public $reference;

    /**
     * Construct RST document node
     *
     * @param ezcDocumentRstToken $token
     * @param ezcDocumentRstTitleNode $title
     * @param int $depth
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token, ezcDocumentRstTitleNode $title = null, $depth = 0 )
    {
        parent::__construct( $token, self::SECTION );

        $this->title = $title;
        $this->depth = $depth;
    }

    /**
     * Return node content, if available somehow
     *
     * @return string
     */
    protected function content()
    {
        return trim( $this->token->content );
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
        $node = new ezcDocumentRstSectionNode(
            $properties['token'],
            $properties['title'],
            $properties['depth']
        );

        $node->nodes = $properties['nodes'];

        return $node;
    }
}

?>
