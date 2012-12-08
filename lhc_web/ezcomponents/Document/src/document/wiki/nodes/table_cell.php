<?php
/**
 * File containing the ezcDocumentWikiTableCellNode struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for Wiki document abstract syntax tree table cell item nodes
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiTableCellNode extends ezcDocumentWikiBlockLevelNode
{
    /**
     * If cell is a header cell
     *
     * @var bool
     */
    public $header = false;

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
        $this->header = $token instanceof ezcDocumentWikiTableHeaderToken;
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
