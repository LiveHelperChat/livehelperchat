<?php
/**
 * File containing the ezcDocumentXhtmlSpecialParagraphElementFilter class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for XHtml table cells.
 *
 * Tables, where the rows are nor structured into a tbody and thead are
 * restructured into those by this filter.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentXhtmlSpecialParagraphElementFilter extends ezcDocumentXhtmlElementBaseFilter
{
    /**
     * Mapping of special paragraph types to their docbook equivalents
     *
     * @var array
     */
    protected $typeMapping = array(
        'note'      => 'note',
        'notice'    => 'tip',
        'warning'   => 'warning',
        'attention' => 'important',
        'danger'    => 'caution',
    );

    /**
     * Filter a single element
     *
     * @param DOMElement $element
     * @return void
     */
    public function filterElement( DOMElement $element )
    {
        foreach ( $this->typeMapping as $class => $type )
        {
            if ( $this->hasClass( $element, $class ) )
            {
                $element->setProperty( 'type', $type );

                // Create a paragraph node wrapping the contents
                $para = $element->ownerDocument->createElement( 'span' );
                $para->setProperty( 'type', 'para' );

                while ( $element->firstChild )
                {
                    $cloned = $element->firstChild->cloneNode( true );
                    $para->appendChild( $cloned );
                    $element->removeChild( $element->firstChild );
                }

                $element->appendChild( $para );
                break;
            }
        }
    }

    /**
     * Check if filter handles the current element
     *
     * Returns a boolean value, indicating weather this filter can handle
     * the current element.
     *
     * @param DOMElement $element
     * @return void
     */
    public function handles( DOMElement $element )
    {
        return ( $element->tagName === 'p' ) &&
               ( $element->hasAttribute( 'class' ) );
    }
}

?>
