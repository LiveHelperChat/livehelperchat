<?php
/**
 * File containing the ezcTreeTransactionItem class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 * @access private
 */

/**
 * A container to store one tree modifying transaction item.
 *
 * @package Tree
 * @version 1.1.4
 * @access private
 */
class ezcTreeTransactionItem extends ezcBaseStruct
{
    /**
     * Used when this transaction deals with adding nodes.
     */
    const ADD = 1;

    /**
     * Used when this transaction deals with deleting nodes.
     */
    const DELETE = 2;

    /**
     * Used when this transaction deals with moving nodes.
     */
    const MOVE = 3;

    /**
     * The item type.
     *
     * Either ADD, DELETE or MOVE.
     *
     * @var int
     */
    public $type;

    /**
     * Contains the node this transaction item is for.
     *
     * Used for "add" items.
     *
     * @var ezcTreeNode
     */
    public $node;

    /**
     * Contains the node ID this transaction item is for.
     *
     * Used for "move" and "delete" items.
     *
     * @var string
     */
    public $nodeId;

    /**
     * Contains the parent node ID this transaction item is for.
     *
     * Used for "add" and "move" items
     *
     * @var string
     */
    public $parentId;

    /**
     * Constructs an ezcTreeTransactionItem object.
     *
     * @param int $type Either ADD, DELETE or REMOVE
     * @param ezcTreeNode $node
     * @param string $nodeId
     * @param string $parentId
     */
    public function __construct( $type, $node = null, $nodeId = null, $parentId = null )
    {
        $this->type = $type;
        $this->node = $node;
        $this->nodeId = $nodeId;
        $this->parentId = $parentId;
    }
}
?>
