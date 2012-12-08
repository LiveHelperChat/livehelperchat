<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * Common interface for all context readers.
 *
 * This interface specifies the methods that a backend should implement if it
 * wants to act as a general purpose translation context reader. It extends on
 * the built-in Iterator interface.
 *
 * Example (see {@link ezcTranslationTsBackend} for a more elaborate example):
 * <code>
 * <?php
 *     $r = new ezcTranslationTsBackend( 'usr/share/translations' );
 *     $r->setOptions( array( 'format' => 'translation-[LOCALE].xml' ) );
 *     $r->initReader( 'nl_NL' );
 *     $r->next();
 *     while ( $r->valid() )
 *     {
 *         $ctxt = $r->current();
 *         $r->next();
 *     }
 *     $r->deinitReader();
 * ?>
 * </code>
 *
 * @package Translation
 * @version 1.3.2
 */
interface ezcTranslationContextRead extends Iterator
{
    /**
     * Initializes the reader to read from the locale $locale.
     *
     * Before starting to request context through the reader, you should call
     * this method to initialize it.
     *
     * @param  string $locale
     * @throws TranslationException when the path of the translation and the
     *                              fileformat of the translation are not set before
     *                              this method is called.
     * @return void
     */
    public function initReader( $locale );

    /**
     * Deinitializes the reader.
     *
     * This method should be called after the haveMore() method returns false
     * to cleanup resources.
     *
     * @throws TranslationException when the reader is not initialized with
     *                              initReader().
     * @return void
     */
    public function deinitReader();
}
?>
