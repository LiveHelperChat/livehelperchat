<?php
/**
 * File containing the abstract ezcDocumentXhtmlElementBaseFilter base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Filter for XHTML elements.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
abstract class ezcDocumentXhtmlElementBaseFilter
{
    /**
     * Filter a single element
     *
     * @param DOMElement $element
     */
    abstract public function filterElement( DOMElement $element );

    /**
     * Check if filter handles the current element
     *
     * Returns a boolean value, indicating weather this filter can handle
     * the current element.
     *
     * @param DOMElement $element
     * @return void
     */
    abstract public function handles( DOMElement $element );

    /**
     * Is block level element
     *
     * Returns true, if the element is a block level element in XHtml, and
     * false otherwise.
     *
     * @param DOMElement $element
     * @return boolean
     */
    protected function isBlockLevelElement( DOMElement $element )
    {
        return in_array(
            $element->tagName,
            array(
                'address',
                'blockquote',
                'body',
                'center',
                'del',
                'dir',
                'div',
                'dl',
                'fieldset',
                'form',
                'h1',
                'h2',
                'h3',
                'h4',
                'h5',
                'h6',
                'hr',
                'ins',
                'li',
                'menu',
                'noframes',
                'noscript',
                'ol',
                'p',
                'pre',
                'table',
                'th',
                'td',
                'ul',
            )
        );
    }

    /**
     * Check if node is an inline element
     *
     * Check if the passed node is an inline element, eg. may occur inside a
     * text block, like a paragraph.
     *
     * @param DOMNode $node
     * @return bool
     */
    protected function isInlineElement( DOMNode $node )
    {
        return (
            ( $node->nodeType === XML_TEXT_NODE ) ||
            ( ( $node->nodeType === XML_ELEMENT_NODE ) &&
              in_array( $node->tagName, array(
                'a',
                'abbr',
                'acronym',
                'applet',
                'b',
                'basefont',
                'bdo',
                'big',
                'button',
                'cite',
                'code',
                'del',
                'dfn',
                'em',
                'font',
                'i',
                'img',
                'ins',
                'input',
                'iframe',
                'kbd',
                'label',
                'map',
                'object',
                'q',
                'samp',
                'script',
                'select',
                'small',
                'span',
                'strong',
                'sub',
                'sup',
                'textarea',
                'tt',
                'var',
              ), true )
            )
        );
    }

    /**
     * Is current element placed inline
     *
     * Checks if the current element is placed inline, which means, it is
     * either a descendant of some other inline element, or part of a
     * paragraph.
     *
     * @param DOMElement $element
     * @return void
     */
    protected function isInline( DOMElement $element )
    {
        $toCheck = $element;
        do {
            if ( in_array( $toCheck->getProperty( 'type' ), array(
                    'para',
                    'subtitle',
                    'title',
                    'term',
                    'abbrev',
                    'acronym',
                    'anchor',
                    'attribution',
                    'author',
                    'citation',
                    'citetitle',
                    'email',
                    'emphasis',
                    'footnoteref',
                    'link',
                    'literal',
                    'literallayout',
                    'quote',
                    'subscript',
                    'superscript',
                    'ulink',
                ) ) )
            {
                return true;
            }
        } while ( ( $toCheck = $toCheck->parentNode ) &&
                  ( $toCheck instanceof DOMElement ) );

        return false;
    }

    /**
     * Check for element class
     *
     * Check if element has the given class in its class attribute. Returns
     * true, if it is contained, or false, if not.
     *
     * @param DOMElement $element
     * @param string $class
     * @return bool
     */
    protected function hasClass( DOMElement $element, $class )
    {
        return ( $element->hasAttribute( 'class' ) &&
                 preg_match(
                    '((?:^|\s)' . preg_quote( $class ) . '(?:\s|$))',
                    $element->getAttribute( 'class' )
                 )
        );
    }

    /**
     * Shows a string representation of the current node.
     *
     * Is only there for debugging purposes
     * 
     * @param DOMElement $element 
     * @param bool $newLine
     * @access private
     */
    protected function showCurrentNode( DOMElement $element, $newLine = true )
    {
        if ( $element->parentNode &&
             ( $element->parentNode instanceof DOMElement ) )
        {
            $this->showCurrentNode( $element->parentNode, false );
        }

        echo '> ', $element->tagName;

        if ( $element->getProperty( 'type' ) !== false )
        {
            echo ' (', $element->getProperty( 'type' ), ')';
        }

        echo $newLine ? "\n" : ' ';
    }
}

?>
