<?php
/**
 * File containing the ezcTemplateTstNode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Node element for parser trees.
 *
 * @property string $originalText The text string contained within the self::$startCursor and self::$endCursor.
 *                                This text is never modified, if the propery is false it will read from the
 *                                source code and set before being returned.
 * @property array(string=>mixed) $treeProperties Array of tree-properties making of the current node, mostly used for debugging.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
abstract class ezcTemplateTstNode
{
    /**
     * @var ezcTemplateSourceCode
     */
    public $source;

    /**
     * @var array
     */
    public $children;

    /**
     * The starting point for the node specified with a cursor
     *
     * @var ezcTemplateCursor
     */
    public $startCursor;

    /**
     * The end point for the node specified with a cursor.
     *
     * @var ezcTemplateCursor
     */
    public $endCursor;

    /**
     * An array containing the properties of this object.
     *
     * @var array(string=>mixed)
     */
    private $properties = array( 'originalText' => false,
                                 'treeProperties' => false );

    /**
     * Initialize with the source code and start/stop cursors.
     *
     * @param ezcTemplateSourceCode $source
     * @param ezcTemplateCursor $start
     * @param ezcTemplateCursor $end
     */
    public function __construct( ezcTemplateSourceCode $source, /*ezcTemplateCursor*/ $start, /*ezcTemplateCursor*/ $end )
    {
        $this->source = $source;
        $this->startCursor = $start;
        $this->endCursor = $end;
    }

    /**
     * Property get
     *
     * @param string $name
     * @return mixed
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'originalText':
                if ( $this->properties[$name] === false )
                    $this->properties[$name] = $this->startCursor->subString( $this->endCursor->position );
                return $this->properties[$name];
            case 'treeProperties':
                return $this->getTreeProperties();
            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Property set
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'originalText':
            case 'treeProperties':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );
            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Property isset
     *
     * @param string $name
     * @return bool
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'originalText':
            case 'treeProperties':
                return true;
            default:
                return false;
        }
    }


    /**
     * Returns the text portion from the original source code which is in the
     * area defined by the start and end cursor.
     *
     * @return string
     */
    public function text()
    {
        return substr( $this->startCursor->text,
                       $this->startCursor->position,
                       $this->endCursor->position - $this->startCursor->position );
    }

    /**
     * Returns an array with all properties related to the node tree.
     *
     * Note: This must be reimplemented by sub-classes.
     *
     * @return array(string=>mixed)
     */
    abstract public function getTreeProperties();

    /**
     * Checks if the current element can be added as child of block object $block,
     * returns true if it can and false if not.
     *
     * @param ezcTemplateBlockTstNode $block The block object which should be the parent.
     * @return bool
     */
    abstract public function canBeChildOf( ezcTemplateBlockTstNode $block );

    /**
     * Figures out the indentation level of the element by checking the
     * whitespace of all lines. The minimum indentation level is returned.
     *
     * @return int
     */
    abstract public function minimumWhitespaceColumn();


    /**
     * Called by the program parser to do custom operations on the new node $element.
     *
     * Note: The default implementation returns false.
     *
     * @param ezcTemplateTstNode $element
     * @return bool
     */
    public function handleElement( ezcTemplateTstNode $element )
    {
        return false;
    }

    /**
     * Checks if the current node can be attached to the parent node $parentElement.
     *
     * @param ezcTemplateTstNode $parentElement
     * @return bool
     */
    public function canAttachToParent( $parentElement )
    {
    }


    /**
     * The accept part for the visitor.
     *
     * The sub classes don't need to implement the usual accept() method.
     *
     * If the current object is: ezcTemplateOutputBlockTstNode then
     * the method: $visitor->visitOutputBlockTstNode( $this ) will be called.
     *
     * @param ezcTemplateTstNodeVisitor $visitor
     * @return ezcTemplateTstNode The result of the visit method on the visitor.
     */
    public function accept( ezcTemplateTstNodeVisitor $visitor  )
    {
        $class = get_class( $this );
        $visit = "visit" . substr( $class, 11 );
        if ( is_callable( array( $visitor, $visit ) ) )
        {
            $res = $visitor->$visit( $this );
            return $res;
        }
        return $this;
    }
}
?>
