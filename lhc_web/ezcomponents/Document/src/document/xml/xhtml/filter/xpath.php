<?php
/**
 * File containing the ezcDocumentXhtmlXpathFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter, which lets you locate the relevant content nodes by a XPath query.
 *
 * The XPath filter extracts the nodes specified by one or more xpath
 * expressions, and replaces the document body with those extracted nodes. This
 * way you may manually extract the relevant content nodes from a website and
 * let the later filters only tranform those.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentXhtmlXpathFilter extends ezcDocumentXhtmlBaseFilter
{
    /**
     * XPath queries used for the content extraction.
     *
     * @var array
     */
    protected $queries = array();

    /**
     * Construct XPath filter
     *
     * Construct the XPath filter, which extracts the nodes specified by one or
     * more xpath expressions, and replaces the document body with those
     * extracted nodes. This way you may manually extract the relevant content
     * nodes from a website and let the later filters only tranform those.
     *
     * You can either pass a single XPath query as a string, or an array of
     * XPath queries to the constructor.
     *
     * @param mixed $xpath
     * @return void
     */
    public function __construct( $xpath = '/*[local-name() = "html"]/*[local-name() = "body"]' )
    {
        $this->queries = ( !is_array( $xpath ) ? array( $xpath ) : $xpath );
    }

    /**
     * Filter XHtml document
     *
     * Filter for the document, which may modify / restructure a document and
     * assign semantic information bits to the elements in the tree.
     *
     * @param DOMDocument $document
     * @return DOMDocument
     */
    public function filter( DOMDocument $document )
    {
        $xpath = new DOMXPath( $document );

        // Extract the target node
        $body = $xpath->query( '/*[local-name() = "html"]/*[local-name() = "body"]' )->item( 0 );

        // Find all content nodes, which should be extracted.
        $nodes = array();
        foreach ( $this->queries as $query )
        {
            $contents = $xpath->query( $query );
            foreach ( $contents as $node )
            {
                $nodes[] = $node->cloneNode( true );
            }
        }

        // Remove all childs from HTML body
        for ( $i = ( $body->childNodes->length - 1 ); $i >= 0; --$i )
        {
            $body->removeChild( $body->childNodes->item( $i ) );
        }

        // Readd all detected content nodes
        foreach ( $nodes as $node )
        {
            $body->appendChild( $node );
        }
    }
}

?>
