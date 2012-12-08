<?php
/**
 * File containing the ezcTreeXmlInternalDataStore class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * ezcTreeXmlInternalDataStore is an implementation of a tree node data store
 * that stores node information in child elements of the XML elements
 * containing the tree nodes.
 *
 * @package Tree
 * @version 1.1.4
 */
class ezcTreeXmlInternalDataStore implements ezcTreeXmlDataStore
{
    /**
     * Contains the DOM representing this tree this data store stores data for.
     *
     * @var DOMDocument
     */
    protected $dom;

    /**
     * Associates the DOM tree for which this data store stores data for with
     * this store.
     *
     * @param DOMDocument $dom
     */
    public function setDomTree( DOMDocument $dom )
    {
        $this->dom = $dom;
    }

    /**
     * Deletes the data for the node $node from the data store.
     *
     * @param ezcTreeNode $node
    public function deleteDataForNode( ezcTreeNode $node )
    {
    }
     */

    /**
     * Deletes the data for all the nodes in the node list $nodeList.
     *
     * @param ezcTreeNodeList $nodeList
     */
    public function deleteDataForNodes( ezcTreeNodeList $nodeList )
    {
        // This is a no-op as the data is part of the nodes
    }

    /**
     * Deletes the data for all the nodes in the store.
     */
    public function deleteDataForAllNodes()
    {
        // This is a no-op as the data is part of the nodes
    }

    /**
     * Retrieves the data for the node $node from the data store and assigns it
     * to the node's 'data' property.
     *
     * @param ezcTreeNode $node
     */
    public function fetchDataForNode( ezcTreeNode $node )
    {
        $id = $node->id;
        $elem = $this->dom->getElementById( "{$node->tree->prefix}{$id}" );
        $dataElem = $elem->getElementsByTagNameNS( 'http://components.ez.no/Tree/data', 'data' )->item( 0 );
        if ( $dataElem === null || ( (string) $dataElem->parentNode->getAttribute( 'id' ) !== "{$node->tree->prefix}{$id}" ) )
        {
            throw new ezcTreeDataStoreMissingDataException( $node->id );
        }

        $node->injectData( $dataElem->firstChild->data );
        $node->dataFetched = true;
    }

    /**
     * Retrieves the data for all the nodes in the node list $nodeList and
     * assigns this data to the nodes' 'data' properties.
     *
     * @param ezcTreeNodeList $nodeList
     */
    public function fetchDataForNodes( ezcTreeNodeList $nodeList )
    {
        foreach ( $nodeList->nodes as $node )
        {
            if ( $node->dataFetched === false )
            {
                $this->fetchDataForNode( $node );
            }
        }
    }

    /**
     * Stores the data in the node to the data store.
     *
     * @param ezcTreeNode $node
     */
    public function storeDataForNode( ezcTreeNode $node )
    {
        // Locate the element
        $id = $node->id;
        $elem = $this->dom->getElementById( "{$node->tree->prefix}{$id}" );

        // Create the new element
        $dataNode = $elem->ownerDocument->createElementNS( 'http://components.ez.no/Tree/data', 'etd:data', $node->data );

        // Locate the data element, and remove it
        $dataElem = $elem->getElementsByTagNameNS( 'http://components.ez.no/Tree/data', 'data' )->item( 0 );
        if ( $dataElem !== null )
        {
            $dataElem->parentNode->replaceChild( $dataNode, $dataElem );
        }
        else
        {
            $elem->appendChild( $dataNode );
        }

        // Create the new data element and add it
        $node->dataStored = true;
        if ( !$node->tree->inTransactionCommit() )
        {
            $node->tree->saveFile();
        }
    }
}
?>
