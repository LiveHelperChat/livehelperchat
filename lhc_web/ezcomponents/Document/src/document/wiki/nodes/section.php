<?php
/**
 * File containing the ezcDocumentWikiSectionNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for Wiki section abstract syntax tree root nodes
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiSectionNode extends ezcDocumentWikiBlockLevelNode
{
    /**
     * Section depth
     *
     * @var int
     */
    public $level = 1;

    /**
     * Construct Wiki node
     *
     * @param ezcDocumentWikiToken $token
     * @param int $type
     * @return void
     */
    public function __construct( ezcDocumentWikiToken $token )
    {
        parent::__construct( $token );

        $this->level = $token instanceof ezcDocumentWikiTitleToken ? $token->level : 0;
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
        $nodeClass = __CLASS__;
        $node = new $nodeClass( $properties['token'] );
        $node->nodes = $properties['nodes'];
        return $node;
    }
}

?>
