<?php
/**
 * File containing the ezcDocumentPdfBlockquoteRenderer class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Renders a blockquote.
 *
 * Renders a blockquote and its attributions. A blockquote is basically an 
 * indented common paragraph, with the styling given by the used CSS file.
 *
 * The annotations precede the actual quote in Docbook, but will be rendered 
 * below the quote by this renderer.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPdfBlockquoteRenderer extends ezcDocumentPdfBlockRenderer
{
    /**
     * Process to render block contents.
     * 
     * @param ezcDocumentPdfPage $page 
     * @param ezcDocumentPdfHyphenator $hyphenator 
     * @param ezcDocumentPdfTokenizer $tokenizer 
     * @param ezcDocumentLocateableDomElement $block 
     * @param ezcDocumentPdfMainRenderer $mainRenderer 
     * @return void
     */
    protected function process( ezcDocumentPdfPage $page, ezcDocumentPdfHyphenator $hyphenator, ezcDocumentPdfTokenizer $tokenizer, ezcDocumentLocateableDomElement $block, ezcDocumentPdfMainRenderer $mainRenderer )
    {
        $childNodes   = $block->childNodes;
        $nodeCount    = $childNodes->length;
        $attributions = array();

        for ( $i = 0; $i < $nodeCount; ++$i )
        {
            $child = $childNodes->item( $i );
            if ( $child->nodeType !== XML_ELEMENT_NODE )
            {
                continue;
            }

            // Default to docbook namespace, if no namespace is defined
            $namespace = $child->namespaceURI === null ? 'http://docbook.org/ns/docbook' : $child->namespaceURI;
            if ( ( $namespace === 'http://docbook.org/ns/docbook' ) &&
                 ( $child->tagName === 'attribution' ) )
            {
                $attributions[] = $child;
                continue;
            }

            $mainRenderer->processNode( $child );
        }

        // Render attributions below the actual quotes
        $textRenderer = new ezcDocumentPdfTextBoxRenderer( $this->driver, $this->styles );
        foreach ( $attributions as $attribution )
        {
            $textRenderer->renderNode( $page, $hyphenator, $tokenizer, $attribution, $mainRenderer );
        }
    }
}

?>
