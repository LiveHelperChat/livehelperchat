<?php
/**
 * File containing the ezcDocumentOdtStyleFilter class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter mechanism based on ODT style information.
 *
 * This filter consists of filte rules, which inference semantics for ODT
 * elements based on their attached style information.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentOdtStyleFilter extends ezcDocumentOdtBaseFilter
{
    /**
     * Style filter rules. 
     * 
     * @var array(ezcDocumentOdtStyleFilterRule)
     */
    protected $rules = array();

    /**
     * Style inferencer. 
     * 
     * @var ezcDocumentOdtStyleInferencer
     */
    protected $styleInferencer;

    /**
     * Creates a new style filter.
     *
     * @todo Make configurable.
     */
    public function __construct()
    {
        $this->rules = array(
            new ezcDocumentOdtEmphasisStyleFilterRule(),
            new ezcDocumentOdtListLevelStyleFilterRule(),
        );
    }

    /**
     * Filter ODT document.
     *
     * Filter for the document, which may modify / restructure a document and
     * assign semantic information bits to the elements in the tree.
     *
     * @param DOMDocument $dom
     * @return DOMDocument
     */
    public function filter( DOMDocument $dom )
    {
        $this->styleInferencer = new ezcDocumentOdtStyleInferencer( $dom );
        $xpath = new DOMXPath( $dom );
        $xpath->registerNamespace( 'office', ezcDocumentOdt::NS_ODT_OFFICE );
        $root = $xpath->query( '//office:body' )->item( 0 );
        $this->filterNode( $root );
    }

    /**
     * Filter node
     *
     * Depending on the element name, it parents and maybe element attributes
     * semantic information is assigned to nodes.
     *
     * @param DOMElement $element
     * @return void
     */
    protected function filterNode( DOMElement $element )
    {
        $style = null;
        foreach ( $this->rules as $rule )
        {
            if ( $rule->handles( $element ) )
            {
                $rule->filter( $element, $this->styleInferencer );
            }
        }

        foreach ( $element->childNodes as $child )
        {
            if ( $child->nodeType === XML_ELEMENT_NODE )
            {
                $this->filterNode( $child );
            }
        }
    }
}

?>
