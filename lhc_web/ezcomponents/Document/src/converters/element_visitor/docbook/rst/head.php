<?php
/**
 * File containing the ezcDocumentDocbookToRstHeadHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit docbook sectioninfo elements
 *
 * The sectioninfo elements contain metadata about the document or
 * sections, which are transformed into the respective metadata in the HTML
 * header.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToRstHeadHandler extends ezcDocumentDocbookToRstBaseHandler
{
    /**
     * Element name mapping for meta information in the docbook document to
     * HTML meta element names.
     *
     * @var array
     */
    protected $headerMapping = array(
        'authors'     => 'Authors',
        'abstract'    => 'Description',
        'copyright'   => 'Copyright',
        'releaseinfo' => 'Version',
        'pubdate'     => 'Date',
        'date'        => 'Date',
        'author'      => 'Author',
        'publisher'   => 'Author',
    );

    /**
     * Handle a node
     *
     * Handle / transform a given node, and return the result of the
     * conversion.
     *
     * @param ezcDocumentElementVisitorConverter $converter
     * @param DOMElement $node
     * @param mixed $root
     * @return mixed
     */
    public function handle( ezcDocumentElementVisitorConverter $converter, DOMElement $node, $root )
    {
        foreach ( $this->headerMapping as $tagName => $metaName )
        {
            if ( ( $nodes = $node->getElementsBytagName( $tagName ) ) &&
                 ( $nodes->length > 0 ) )
            {
                foreach ( $nodes as $child )
                {
                    $root .= ":$metaName:\n";
                    $root .= ezcDocumentDocbookToRstConverter::wordWrap( trim( $converter->visitChildren( $child, '' ) ), 2 );
                    $root .= "\n";
                }
            }
        }

        return $root;
    }
}

?>
