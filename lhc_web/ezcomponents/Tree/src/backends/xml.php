<?php
/**
 * File containing the ezcTreeXml class.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.4
 * @filesource
 * @package Tree
 */

/**
 * ezcTreeXml is an implementation of a tree backend that operates on
 * an XML file.
 *
 * Example:
 * <code>
 * <?php
 *     // Create a new tree in a new XML file
 *     $tree = ezcTreeXml::create(
 *         'new-tree.xml', 
 *         new ezcTreeXmlInternalDataStore()
 *     );
 * 
 *     // Open an existing XML file containing a tree structure
 *     $tree = new ezcTreeXml(  
 *         'existing-tree.xml',
 *         new ezcTreeXmlInternalDataStore()
 *     );
 * ?>
 * </code>
 *
 * See {@link ezcTree} for examples on how to operate on the tree.
 *
 * @property-read ezcTreeXmlDataStore $store
 *                The data store that is used for retrieving/storing data.
 * @property-read string              $prefix
 *                The prefix that is used to prefix node IDs with in the tree.
 * @property      string              $nodeClassName
 *                Which class is used as tree node - this class *must* inherit
 *                the ezcTreeNode class.
 *
 * @package Tree
 * @version 1.1.4
 * @mainclass
 */
class ezcTreeXml extends ezcTree
{
    /**
     * Contains the relax-NG schema to validate the tree XML.
     */
    const relaxNG = '<?xml version="1.0" encoding="UTF-8"?>
<grammar xmlns:etd="http://components.ez.no/Tree/data" ns="http://components.ez.no/Tree" xmlns="http://relaxng.org/ns/structure/1.0" datatypeLibrary="http://www.w3.org/2001/XMLSchema-datatypes">
  <start>
    <element name="tree">
      <optional>
        <attribute name="prefix">
          <data type="ID"/>
        </attribute>
      </optional>
      <optional>
        <attribute name="lastNodeId">
          <data type="string"/>
        </attribute>
      </optional>
      <optional>
        <ref name="node"/>
      </optional>
    </element>
  </start>
  <define name="node">
    <element name="node">
      <attribute name="id">
        <data type="ID"/>
      </attribute>
      <optional>
        <element name="etd:data">
          <text/>
        </element>
      </optional>
      <zeroOrMore>
        <ref name="node"/>
      </zeroOrMore>
    </element>
  </define>
</grammar>';

    /**
     * Contains the DOM Tree that all operations will be done on.
     *
     * When the tree object is constructed the XML is parsed with DOM and
     * stored into this member variable. When the tree is modified the changes
     * are then flushed to disk with the saveFile() method.
     *
     * @var DOMDocument $dom
     */
    private $dom;

    /**
     * The file name that contains the tree as XML string.
     *
     * @var string $xmlFile
     */
    private $xmlFile;

    /**
     * Stores the last auto generated ID that was used.
     *
     * @var integer $autoNodeId
     */
    private $autoNodeId = 0;

    /**
     * Constructs a new ezcTreeXml object from the XML data in $xmlFile and using
     * the $store to retrieve data from.
     *
     * @param string $xmlFile
     * @param ezcTreeXmlDataStore $store
     */
    public function __construct( $xmlFile, ezcTreeXmlDataStore $store )
    {
        if ( !file_exists( $xmlFile ) )
        {
            throw new ezcBaseFileNotFoundException( $xmlFile, "XML" );
        }

        $previous = libxml_use_internal_errors( true );

        $dom = new DomDocument();
        $dom->load( $xmlFile );
        $dom->formatOutput = true;

        $errors = libxml_get_errors();
        libxml_clear_errors();
        if ( count( $errors ) )
        {
            throw new ezcTreeInvalidXmlException( $xmlFile, $errors );
        }

        $valid = $dom->relaxNGValidateSource( self::relaxNG );
        if ( !$valid )
        {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new ezcTreeInvalidXmlFormatException( $xmlFile, $errors );
        }

        libxml_use_internal_errors( $previous );

        // Associate the DOM tree with the data store
        $store->setDomTree( $dom );

        // Figure out the prefix - which is the "prefix" attribute on the root node.
        $document = $dom->documentElement;
        $prefix = $document->getAttribute( 'prefix' );

        // Figure out the last auto generated ID
        $autoId = false;
        $this->autoNodeId = $document->getAttribute( 'lastNodeId' );
        if ( $this->autoNodeId !== "" )
        {
            $autoId = true;
        }

        // Set member variables
        $this->dom = $dom;
        $this->xmlFile = $xmlFile;
        $this->properties['store'] = $store;
        $this->properties['prefix'] = $prefix;
        $this->properties['autoId'] = $autoId;
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'prefix':
                return $this->properties[$name];
        }
        return parent::__get( $name );
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @throws ezcBasePropertyPermissionException if a read-only property is
     *         tried to be modified.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'prefix':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );

            default:
                return parent::__set( $name, $value );
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name     
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'prefix':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Creates a new XML tree in the file $xmlFile using $store as data store.
     *
     * The $prefix option can be used to change the prefix that is used for IDs
     * in the created tree. By default this is set to "id" so that numerical
     * node IDs are not a problem.
     *
     * @param string $xmlFile
     * @param ezcTreeXmlDataStore $store
     * @param string $prefix
     * @return ezcTreeXml
     */
    public static function create( $xmlFile, ezcTreeXmlDataStore $store, $prefix = 'id' )
    {
        $dom = new DomDocument( '1.0', 'utf-8' );
        $dom->formatOutput = true;

        $element = $dom->createElement( 'tree' );
        $element->setAttributeNode( new DOMAttr( 'xmlns', 'http://components.ez.no/Tree' ) );
        $element->setAttributeNode( new DOMAttr( 'xmlns:etd', 'http://components.ez.no/Tree/data' ) );
        if ( $prefix !== null )
        {
            $element->setAttributeNode( new DOMAttr( 'prefix', $prefix ) );
        }

        $dom->appendChild( $element );

        $dom->save( $xmlFile );
        return new ezcTreeXml( $xmlFile, $store, $prefix );
    }

    /**
     * Saves the internal DOM representation of the tree back to disk.
     */
    public function saveFile()
    {
        $this->dom->save( $this->xmlFile );
    }

    /**
     * This method generates the next node ID.
     *
     * It uses the stored last generated ID, and also stores this in an
     * attribute on the root node so that it can be correctly incremented
     * without having to search for the last generated ID in the whole tree.
     *
     * @return integer
     */
    protected function generateNodeID()
    {
        $this->autoNodeId++;

        $document = $this->dom->documentElement;
        $document->setAttributeNode( new DOMAttr( 'lastNodeId', $this->autoNodeId ) );

        return $this->autoNodeId;
    }

    /**
     * Returns whether the node with ID $nodeId exists.
     *
     * @param string $nodeId
     * @return bool
     */
    public function nodeExists( $nodeId )
    {
        $elem = $this->dom->getElementById( "{$this->properties['prefix']}$nodeId" );
        return ( $elem !== null ) ? true : false;
    }

    /**
     * Retrieves a DOMElement containing the node with node ID $nodeId.
     *
     * @param string $nodeId
     * @return DOMElement
     */
    private function getNodeById( $nodeId )
    {
        $node = $this->dom->getElementById( "{$this->properties['prefix']}$nodeId" );
        if ( !$node )
        {
            throw new ezcTreeUnknownIdException( $nodeId );
        }
        return $node;
    }

    /**
     * Fetches all the child "node" DOM elements of the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return array(string)
     */
    private function fetchChildIds( $nodeId )
    {
        $childNodes = array();
        $elem = $this->getNodeById( $nodeId );
        $children = $elem->childNodes;
        foreach ( $children as $child )
        {
            if ( $child->nodeType === XML_ELEMENT_NODE && $child->tagName == "node" )
            {
                $nodeId = substr( $child->getAttribute( 'id' ), strlen( $this->properties['prefix'] ) );
                $childNodes[] = $nodeId;
            }
        }
        return $childNodes;
    }

    /**
     * Returns all the children of the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchChildren( $nodeId )
    {
        $className = $this->properties['nodeClassName'];
        $list = new ezcTreeNodeList;
        foreach ( $this->fetchChildIds( $nodeId ) as $childId )
        {
            $list->addNode( new $className( $this, $childId ) );
        }
        return $list;
    }

    /**
     * Returns the parent node of the node with ID $nodeId.
     *
     * This method returns null if there is no parent node.
     *
     * @param string $nodeId
     * @return ezcTreeNode
     */
    public function fetchParent( $nodeId )
    {
        $className = $this->properties['nodeClassName'];
        $elem = $this->getNodeById( $nodeId );
        $elem = $elem->parentNode;
        $parentId = $elem !== null ? substr( $elem->getAttribute( 'id' ), strlen( $this->properties['prefix'] ) ) : null;
        if ( $parentId === false )
        {
            return null;
        }
        return new $className( $this, $parentId );
    }

    /**
     * Returns all the nodes in the path from the root node to the node with ID
     * $nodeId, including those two nodes.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchPath( $nodeId )
    {
        $className = $this->properties['nodeClassName'];

        $nodes = array();
        $nodes[] = new $className( $this, $nodeId );

        $elem = $this->getNodeById( $nodeId );
        $elem = $elem->parentNode;

        while ( $elem !== null && $elem->nodeType == XML_ELEMENT_NODE && $elem->tagName == 'node' )
        {
            $id = substr( $elem->getAttribute( 'id' ), strlen( $this->properties['prefix'] ) );
            $nodes[] = new $className( $this, $id );
            $elem = $elem->parentNode;
        }

        $list = new ezcTreeNodeList;
        foreach ( array_reverse( $nodes ) as $node )
        {
            $list->addNode( $node );
        }
        return $list;
    }

    /**
     * Adds the children nodes of the node with ID $nodeId to the
     * ezcTreeNodeList $list.
     *
     * @param ezcTreeNodeList $list
     * @param string          $nodeId
     */
    private function addChildNodesDepthFirst( ezcTreeNodeList $list, $nodeId )
    {
        $className = $this->properties['nodeClassName'];
        foreach ( $this->fetchChildIds( $nodeId ) as $childId )
        {
            $list->addNode( new $className( $this, $childId ) );
            $this->addChildNodesDepthFirst( $list, $childId );
        }
    }

    /**
     * Returns the node with ID $nodeId and all its children, sorted according to
     * the {@link http://en.wikipedia.org/wiki/Depth-first_search Depth-first sorting}
     * algorithm.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchSubtreeDepthFirst( $nodeId )
    {
        $className = $this->properties['nodeClassName'];
        $list = new ezcTreeNodeList;
        $list->addNode( new $className( $this, $nodeId ) );
        $this->addChildNodesDepthFirst( $list, $nodeId );
        return $list;
    }

    /**
     * Alias for fetchSubtreeDepthFirst().
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchSubtree( $nodeId )
    {
        return $this->fetchSubtreeDepthFirst( $nodeId );
    }

    /**
     * Adds the children nodes of the node with ID $nodeId to the
     * ezcTreeNodeList $list.
     *
     * @param ezcTreeNodeList $list
     * @param string          $nodeId
     */
    private function addChildNodesBreadthFirst( ezcTreeNodeList $list, $nodeId )
    {
        $className = $this->properties['nodeClassName'];
        $childIds = $this->fetchChildIds( $nodeId );
        foreach ( $childIds as $childId )
        {
            $list->addNode( new $className( $this, $childId ) );
        }
        foreach ( $childIds as $childId )
        {
            $this->addChildNodesDepthFirst( $list, $childId );
        }
    }

    /**
     * Returns the node with ID $nodeId and all its children, sorted according to
     * the {@link http://en.wikipedia.org/wiki/Breadth-first_search Breadth-first sorting}
     * algorithm.
     *
     * @param string $nodeId
     * @return ezcTreeNodeList
     */
    public function fetchSubtreeBreadthFirst( $nodeId )
    {
        $className = $this->properties['nodeClassName'];
        $list = new ezcTreeNodeList;
        $list->addNode( new $className( $this, $nodeId ) );
        $this->addChildNodesBreadthFirst( $list, $nodeId );
        return $list;
    }

    /**
     * Returns the number of direct children of the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return int
     */
    public function getChildCount( $nodeId )
    {
        $count = 0;
        $elem = $this->getNodeById( $nodeId );
        $children = $elem->childNodes;
        foreach ( $children as $child )
        {
            if ( $child->nodeType === XML_ELEMENT_NODE && $child->tagName == "node" )
            {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Adds the number of children with for the node with ID $nodeId nodes to
     * $count, recursively.
     *
     * @param int $count
     * @param string $nodeId
     */
    private function countChildNodes( &$count, $nodeId )
    {
        foreach ( $this->fetchChildIds( $nodeId ) as $childId )
        {
            $count++;
            $this->countChildNodes( $count, $childId );
        }
    }

    /**
     * Returns the number of children of the node with ID $nodeId, recursively.
     *
     * @param string $nodeId
     * @return int
     */
    public function getChildCountRecursive( $nodeId )
    {
        $count = 0;
        $this->countChildNodes( $count, $nodeId );
        return $count;
    }

    /**
     * Returns the distance from the root node to the node with ID $nodeId.
     *
     * @param string $nodeId
     * @return int
     */
    public function getPathLength( $nodeId )
    {
        $elem = $this->getNodeById( $nodeId );
        $elem = $elem->parentNode;
        $length = -1;

        while ( $elem !== null && $elem->nodeType == XML_ELEMENT_NODE )
        {
            $elem = $elem->parentNode;
            $length++;
        }
        return $length;
    }

    /**
     * Returns whether the node with ID $nodeId has children.
     *
     * @param string $nodeId
     * @return bool
     */
    public function hasChildNodes( $nodeId )
    {
        $elem = $this->getNodeById( $nodeId );
        $children = $elem->childNodes;
        foreach ( $children as $child )
        {
            if ( $child->nodeType === XML_ELEMENT_NODE && $child->tagName == "node" )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns whether the node with ID $childId is a direct child of the node
     * with ID $parentId.
     *
     * @param string $childId
     * @param string $parentId
     * @return bool
     */
    public function isChildOf( $childId, $parentId )
    {
        $elem = $this->getNodeById( $childId );
        $parentElem = $elem->parentNode;
        $nodeId = $parentElem->getAttribute( 'id' );
        if ( $nodeId === "{$this->properties['prefix']}$parentId" )
        {
            return true;
        }
        return false;
    }

    /**
     * Returns whether the node with ID $childId is a direct or indirect child
     * of the node with ID $parentId.
     *
     * @param string $childId
     * @param string $parentId
     * @return bool
     */
    public function isDescendantOf( $childId, $parentId )
    {
        $elem = $this->getNodeById( $childId );
        $elem = $elem->parentNode;

        while ( $elem !== null && $elem->nodeType == XML_ELEMENT_NODE )
        {
            $nodeId = $elem->getAttribute( 'id' );
            if ( $nodeId === "{$this->properties['prefix']}$parentId" )
            {
                    return true;
            }
            $elem = $elem->parentNode;
        }
        return false;
    }

    /**
     * Returns whether the nodes with IDs $child1Id and $child2Id are siblings
     * (ie, they share the same parent).
     *
     * @param string $child1Id
     * @param string $child2Id
     * @return bool
     */
    public function isSiblingOf( $child1Id, $child2Id )
    {
        $elem1 = $this->getNodeById( $child1Id );
        $elem2 = $this->getNodeById( $child2Id );
        return (
            ( $child1Id !== $child2Id ) && 
            ( $elem1->parentNode->getAttribute( 'id' ) === $elem2->parentNode->getAttribute( 'id' ) )
        );
    }

    /**
     * Sets a new node as root node, this also wipes out the whole tree.
     *
     * @param ezcTreeNode $node
     */
    public function setRootNode( ezcTreeNode $node )
    {
        $document = $this->dom->documentElement;

        // remove old root node(s)
        foreach ( $document->childNodes as $childNode )
        {
            if ( $childNode->nodeType == XML_ELEMENT_NODE && $childNode->tagName === 'node' )
            {
                $nodeId = substr( $childNode->getAttribute( 'id' ), strlen( $this->properties['prefix'] ) );
                $this->delete( $nodeId );
            }
        }
        $this->store->deleteDataForAllNodes();

        // Create new root node
        $root = $this->dom->createElement( 'node' );
        $root->setAttributeNode( new DOMAttr( 'id', "{$this->properties['prefix']}{$node->id}" ) );
        $root->setIdAttribute( 'id', true );
        $document->appendChild( $root );
        $this->store->storeDataForNode( $node, $node->data );
        $this->saveFile();
    }

    /**
     * Returns the root node.
     *
     * This methods returns null if there is no root node.
     *
     * @return ezcTreeNode
     */
    public function getRootNode()
    {
        $className = $this->properties['nodeClassName'];
        $document = $this->dom->documentElement;

        foreach ( $document->childNodes as $childNode )
        {
            if ( $childNode->nodeType == XML_ELEMENT_NODE && $childNode->tagName == 'node' )
            {
                $nodeId = substr( $childNode->getAttribute( 'id' ), strlen( $this->properties['prefix'] ) );
                return new $className( $this, $nodeId );
            }
        }
        return null;
    }

    /**
     * Adds the node $childNode as child of the node with ID $parentId.
     *
     * @param string $parentId
     * @param ezcTreeNode $childNode
     */
    public function addChild( $parentId, ezcTreeNode $childNode )
    {
        if ( $this->inTransaction )
        {
            $this->addTransactionItem( new ezcTreeTransactionItem( ezcTreeTransactionItem::ADD, $childNode, null, $parentId ) );
            return;
        }

        // locate parent node
        $elem = $this->getNodeById( $parentId );

        // Create new DOM node
        $child = $this->dom->createElement( 'node' );
        $child->setAttributeNode( new DOMAttr( 'id', "{$this->properties['prefix']}{$childNode->id}" ) );
        $child->setIdAttribute( 'id', true );

        // Append to parent node
        $elem->appendChild( $child );
        $this->store->storeDataForNode( $childNode, $childNode->data );

        if ( !$this->inTransactionCommit )
        {
            $this->saveFile();
        }
    }

    /**
     * Deletes the node with ID $nodeId from the tree, including all its children.
     *
     * @param string $nodeId
     */
    public function delete( $nodeId )
    {
        if ( $this->inTransaction )
        {
            $this->addTransactionItem( new ezcTreeTransactionItem( ezcTreeTransactionItem::DELETE, null, $nodeId ) );
            return;
        }

        // Delete all the associated data
        $nodeList = $this->fetchSubtree( $nodeId );
        $this->store->deleteDataForNodes( $nodeList );

        // locate node to move
        $nodeToDelete = $this->getNodeById( $nodeId );

        // Remove the ID on all children by hand as this would crash in PHP <= 5.2.3
        $nodeToDelete->removeAttribute( "id" );

        $children = $nodeToDelete->getElementsByTagName( 'node' );
        foreach ( $children as $child )
        {
            $child->removeAttribute( "id" );
        }

        // Use the parent to remove the child
        $nodeToDelete->parentNode->removeChild( $nodeToDelete );

        if ( !$this->inTransactionCommit )
        {
            $this->saveFile();
        }
    }

    /**
     * Moves the node with ID $nodeId as child to the node with ID $targetParentId.
     *
     * @param string $nodeId
     * @param string $targetParentId
     */
    public function move( $nodeId, $targetParentId )
    {
        if ( $this->inTransaction )
        {
            $this->addTransactionItem( new ezcTreeTransactionItem( ezcTreeTransactionItem::MOVE, null, $nodeId, $targetParentId ) );
            return;
        }

        // locate node to move
        $nodeToMove = $this->getNodeById( $nodeId );

        // locate new parent
        $parent = $this->getNodeById( $targetParentId );

        $parent->appendChild( $nodeToMove );

        if ( !$this->inTransactionCommit )
        {
            $this->saveFile();
        }
    }

    /**
     * Fixates the transaction (saves the XML file).
     */
    public function fixateTransaction()
    {
        $this->saveFile();
    }
}
?>
