<?php
/**
 * File containing the ezcDocumentRstBlockquoteNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The blockquote AST node
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentRstBlockquoteNode extends ezcDocumentRstBlockNode
{
    /**
     * Blockquote annotation
     *
     * @var ezcDocumentRstBlockquoteAnnotationNode
     */
    public $annotation = null;

    /**
     * Indicator telling whether a blockquote has been finished by either a
     * annotation or an explicit blockquote separation marker.
     *
     * @var bool
     */
    public $closed = false;

    /**
     * Construct RST document node
     *
     * @param ezcDocumentRstToken $token
     * @return void
     */
    public function __construct( ezcDocumentRstToken $token )
    {
        // Perhaps check, that only node of type section and metadata are
        // added.
        parent::__construct( $token, self::BLOCKQUOTE );
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
        $node = new ezcDocumentRstBlockquoteNode(
            $properties['token']
        );

        $node->nodes       = $properties['nodes'];
        $node->annotation  = $properties['annotation'];
        $node->closed      = $properties['closed'];
        $node->indentation = isset( $properties['indentation'] ) ? $properties['indentation'] : 0;
        return $node;
    }
}

?>
