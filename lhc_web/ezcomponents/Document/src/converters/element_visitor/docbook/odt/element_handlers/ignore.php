<?php
/**
 * File containing ezcDocumentDocbookToOdtIgnoreHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Handler for elements, which are safe to be ignored.
 *
 * This ignore handler can either ignore only a single XML element level or can 
 * be configured to ignore a complete XML sub-tree.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentDocbookToOdtIgnoreHandler extends ezcDocumentDocbookToOdtBaseHandler
{
    /**
     * If child elements should also be ignored. 
     * 
     * @var bool
     */
    protected $deepIgnore;

    /**
     * Creates a new ignore handler.
     *
     * If $deepIgnore is set to true, child elements of the ignored element 
     * will also not be visited. 
     * 
     * @param ezcDocumentOdtStyler $styler
     * @param bool $deepIgnore 
     */
    public function __construct( ezcDocumentOdtStyler $styler, $deepIgnore = false )
    {
        parent::__construct( $styler );
        $this->deepIgnore = $deepIgnore;
    }

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
        if ( !$this->deepIgnore )
        {
            return $converter->visitChildren( $node, $root );
        }
        return $root;
    }
}

?>
