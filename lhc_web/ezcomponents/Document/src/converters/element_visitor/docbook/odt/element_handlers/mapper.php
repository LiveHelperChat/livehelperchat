<?php
/**
 * File containing the ezcDocumentDocbookToOdtMappingHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Simple mapping handler
 *
 * Performs a simple 1 to 1 mapping between DocBook elements and ODT elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentDocbookToOdtMappingHandler extends ezcDocumentDocbookToOdtBaseHandler
{
    /**
     * Mapping of element names.
     *
     * Mapping from DocBook to ODT elements. The local name of a DocBook 
     * element is used as the key to look up a corresponding element in ODT.  
     * Since ODT utilizes multiple namespaces, an array of namespace and local 
     * name for the target element is returned.
     *
     * @var array(string=>array(string))
     */
    protected $mapping = array(
        'listitem' => array( ezcDocumentOdt::NS_ODT_TEXT, 'text:list-item' )
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
        if ( !isset( $this->mapping[$node->localName] ) )
        {
            // This only occurs if the mapper is assigned to an unknown 
            // element, which should not happen at all.
            throw new ezcDocumentMissingVisitorException(
                $node->localName
            );
        }

        $targetElementData = $this->mapping[$node->localName];

        $targetElement = $root->appendChild(
            $root->ownerDocument->createElementNS(
                $targetElementData[0],
                $targetElementData[1]
            )
        );

        $this->styler->applyStyles( $node, $targetElement );

        $converter->visitChildren( $node, $targetElement );
        return $root;
    }
}

?>
