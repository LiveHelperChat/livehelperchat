<?php
/**
 * File containing the ezcDocumentConfluenceWiki class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Document handler for Confluence wiki text documents.
 *
 * Confluence is the wiki syntax used in the Atlassian wiki project Confluence.
 * Its syntax differs quite a lot from other common wiki markup syntaxes. The
 * markup language is documented at:
 *
 * http://confluence.atlassian.com/renderer/notationhelp.action?section=all
 *
 * This document handler implements conversions for Crole wiki markup.
 * The tokenizer, which differs for each wiki language, can be set
 * directly, or you may use on of the other extended implementations for the
 * specific sytaxes:
 *
 * - ezcDocumentConfluenceWiki
 * - ezcDocumentCreoleWiki
 * - ezcDocumentDokuwikiWiki
 *
 * Each wiki syntax has some sort of plugin mechanism, which allows you to
 * handle the contents of a special formatted syntax element using custom
 * classes or external applications. You can register a plugin for this, which
 * then need to "parse" the element contents itself and may return random
 * docbook markup.
 *
 * The basic conversion of a wiki document into a docbook document, using the
 * default creole tokenizer, looks like:
 *
 * <code>
 *  $document = new ezcDocumentConfluenceWiki();
 *  $document->loadString( '
 *  = Example text =
 *
 *  Just some exaple paragraph with a heading, some **emphasis** markup and a
 *  [[http://ezcomponents.org|link]].' );
 *
 *  $docbook = $document->getAsDocbook();
 *  echo $docbook->save();
 * </code>
 *
 * A converter for the conversion from docbook back to confluence wiki markup
 * has not yet been implemented.
 *
 * @package Document
 * @version 1.3.1
 * @mainclass
 */
class ezcDocumentConfluenceWiki extends ezcDocumentWiki
{
    /**
     * Construct RST document.
     *
     * @ignore
     * @param ezcDocumentWikiOptions $options
     * @return void
     */
    public function __construct( ezcDocumentWikiOptions $options = null )
    {
        parent::__construct( $options === null ?
            new ezcDocumentWikiOptions() :
            $options );

        $this->options->tokenizer = new ezcDocumentWikiConfluenceTokenizer();
    }

    /**
     * Create document from docbook document
     *
     * A document of the docbook format is provided and the internal document
     * structure should be created out of this.
     *
     * This method is required for all formats to have one central format, so
     * that each format can be compiled into each other format using docbook as
     * an intermediate format.
     *
     * You may of course just call an existing converter for this conversion.
     *
     * @param ezcDocumentDocbook $document
     * @return void
     */
    public function createFromDocbook( ezcDocumentDocbook $document )
    {
        throw new ezcDocumentMissingVisitorException( get_class( $document ) );
    }
}

?>
