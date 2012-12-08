<?php
/**
 * File containing the ezcDocumentCreoleWiki class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Document handler for Creole wiki text documents.
 *
 * Creole wiki markup is a standardisation intiative for wiki markup languages,
 * which all differ more or less slightly in the used markup syntax. The
 * documentation can be found at:
 *
 * http://www.wikicreole.org/wiki/Home
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
 *  $document = new ezcDocumentCreoleWiki();
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
 * For the conversion back from docbook to wiki markup, currently only one
 * converter to creole markup has been implemented. This conversion can be used
 * like:
 *
 * <code>
 *  $docbook = new ezcDocumentDocbook();
 *  $docbook->loadFile( 'docbook.xml' );
 *
 *  $document = new ezcDocumentCreoleWiki();
 *  $document->createFromDocbook( $docbook );
 *  echo $document->save();
 * </code>
 *
 * @package Document
 * @version 1.3.1
 * @mainclass
 */
class ezcDocumentCreoleWiki extends ezcDocumentWiki
{
}

?>
