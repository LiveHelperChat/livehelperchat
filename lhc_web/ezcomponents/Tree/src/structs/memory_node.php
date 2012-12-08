<?php
/**
 * File containing the ezcTreeMemoryNode class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 * @access private
 */

/**
 * A container to store one memory tree node with meta data, for use with
 * the ezcTreeMemory backend.
 *
 * @package Tree
 * @version 1.1.4
 * @access private
 */
class ezcTreeMemoryNode extends ezcBaseStruct
{
    /**
     * The parent ezcTreeMemoryNode
     *
     * @var ezcTreeMemoryNode
     */
    public $parent;

    /**
     * The encapsulated ezcTreeNode
     *
     * @var ezcTreeNode
     */
    public $node;

    /**
     * Contains the children of this node
     *
     * @var array(string=>ezcTreeMemoryNode)
     */
    public $children;

    /**
     * Constructs an ezcTreeMemoryNode object.
     *
     * @param ezcTreeNode       $node
     * @param array(string=>ezcTreeMemoryNode) $children
     * @param ezcTreeMemoryNode $parent
     */
    public function __construct( ezcTreeNode $node, array $children, ezcTreeMemoryNode $parent = null  )
    {
        $this->node = $node;
        $this->children = $children;
        $this->parent = $parent;
    }
}
?>
