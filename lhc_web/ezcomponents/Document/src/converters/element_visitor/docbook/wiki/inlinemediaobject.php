<?php
/**
 * File containing the ezcDocumentDocbookToWikiInlineMediaObjectHandler class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visit inline media objects
 *
 * Inline media objects are all kind of other media types, embedded in
 * paragraphs, like images.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentDocbookToWikiInlineMediaObjectHandler extends ezcDocumentDocbookToWikiMediaObjectHandler
{
    /**
     * Handle a node.
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
        $image = $this->getImageParameters( $converter, $node );

        if ( isset( $image['alt'] ) || isset( $image['text'] ) )
        {
            $root .= sprintf( '{{%s|%s}}',
                $image['resource'],
                isset( $image['text'] ) ? $image['text'] : $image['alt']
            );
        }
        else
        {
            $root .= sprintf( '{{%s}}', $image['resource'] );
        }

        return $root;
    }
}

?>
