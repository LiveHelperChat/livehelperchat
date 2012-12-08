<?php
/**
 * File containing the abstract ezcDocumentElementVisitorConverter base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Basic converter which stores a list of handlers for each node in the docbook
 * element tree. Those handlers will be executed for the elements, when found.
 * The handler can then handle the repective subtree.
 *
 * Additional handlers may be added by the user to the converter class.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentElementVisitorConverter extends ezcDocumentConverter
{
    /**
     * Element handlers
     *
     * Element handlers for elements per namespace. The namespace names may be
     * names, which might have document specific meaning, like "docbook" for
     * all different docbook versions, or a namespace URI.
     *
     * The handler is as an object of a class inheriting from
     * ezcDocumentDocbookElementVisitorHandler.
     *
     * @var array
     */
    protected $visitorElementHandler = array();

    /**
     * Deafult document namespace
     *
     * If no namespace has been explicitely declared in the source document
     * assume this as the defalt namespace.
     *
     * @var string
     */
    protected $defaultNamespace = 'docbook';

    /**
     * Opject storage to check for reprocessing of DOMNodes, which may cause
     * error which are hard to debug.
     *
     * @var SplObjectStorage
     */
    protected $storage;

    /**
     * Convert documents between two formats
     *
     * Convert documents of the given type to the requested type.
     *
     * @param ezcDocumentDocbook $source
     * @return ezcDocumentDocument
     */
    public function convert( $source )
    {
        $destination = $this->initializeDocument();

        $destination = $this->visitChildren(
            $source->getDomDocument(),
            $destination
        );

        return $this->createDocument( $destination );
    }

    /**
     * Initialize destination document
     *
     * Initialize the structure which the destination document could be build
     * with. This may be an initial DOMDocument with some default elements, or
     * a string, or something else.
     *
     * @return mixed
     */
    abstract protected function initializeDocument();

    /**
     * Create document from structure
     *
     * Build a ezcDocumentDocument object from the structure created during the
     * visiting process.
     *
     * @param mixed $content
     * @return ezcDocumentDocument
     */
    abstract protected function createDocument( $content );

    /**
     * Recursively visit children of a document node.
     *
     * Recurse through the whole document tree and call the defined callbacks
     * for node transformations, defined in the class property
     * $visitorElementHandler.
     *
     * @param DOMNode $node
     * @param mixed $root
     * @return mixed
     */
    public function visitChildren( DOMNode $node, $root )
    {
        if ( $this->storage === null )
        {
            $this->storage = new SplObjectStorage();
        }

        // Recurse into child elements
        foreach ( $node->childNodes as $child )
        {
            if ( $this->storage->contains( $child ) )
            {
                $this->triggerError( E_WARNING, "Duplicate node processing '{$child->tagName}'." );
                continue;
            }
            else
            {
                $this->storage->attach( $child );
            }

            $root = $this->visitNode( $child, $root );
        }

        return $root;
    }

    /**
     * Visit a single document node
     *
     * Visit a single document node and look up the correct visitor and us it
     * to handle the node.
     *
     * @param DOMNode $node
     * @param mixed $root
     * @return mixed
     */
    public function visitNode( DOMNode $node, $root )
    {
        switch ( $node->nodeType )
        {
            case XML_ELEMENT_NODE:
                $root = $this->visitElement( $node, $root );
                break;

            case XML_TEXT_NODE:
                $root = $this->visitText( $node, $root );
                break;
        }

        return $root;
    }

    /**
     * Visit DOMElement nodes.
     * 
     * @param DOMNode $node 
     * @param mixed $root 
     * @return void
     */
    protected function visitElement( DOMElement $node, $root )
    {
        if ( isset( $this->visitorElementHandler[$this->defaultNamespace][$node->tagName] ) )
        {
            $root = $this->visitorElementHandler[$this->defaultNamespace][$node->tagName]->handle(
                $this,
                $node,
                $root
            );
        }
        else
        {
            // Trigger notice for unhandled elements
            $this->triggerError( E_NOTICE, "Unhandled element '{$node->tagName}'." );

            // Recurse into element nodes anyways
            $this->visitChildren( $node, $root );
        }
        return $root;
    }

    /**
     * Visit text node.
     *
     * Visit a text node in the source document and transform it to the
     * destination result
     *
     * @param DOMText $text
     * @param mixed $root
     * @return mixed
     */
    abstract protected function visitText( DOMText $text, $root );

    /**
     * Set custom element handler
     *
     * Set handler for yet unhandled element or overwrite the handler of an
     * existing element.
     *
     * @param string $namespace
     * @param string $element
     * @param ezcDocumentElementVisitorHandler $handler
     * @return void
     */
    public function setElementHandler( $namespace, $element, ezcDocumentElementVisitorHandler $handler )
    {
        $this->visitorElementHandler[$namespace][$element] = $handler;
    }
}

?>
