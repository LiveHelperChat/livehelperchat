<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * Common interface for all context writers.
 *
 * This interface specifies the methods that a backend should implement if it
 * wants to act as a general purpose translation context writer.
 *
 * For an example see {@link ezcTranslationCacheBackend}.
 *
 * @package Translation
 * @version 1.3.2
 */
interface ezcTranslationContextWrite
{
    /**
     * Initializes the writer to write from the locale $locale.
     *
     * Before starting to writer contexts to the writer, you should call
     * this method to initialize it.
     *
     * @param string $locale
     * @throws TranslationException when the path of the translation and the
     *                              translation format are not set before this
     *                              method is called.
     * @return void
     */
    public function initWriter( $locale );

    /**
     * Deinitializes the writer
     *
     * This method should be called after the last context was written to
     * cleanup resources.
     *
     * @throws TranslationException when the writer is not initialized with
     *                              initWriter().
     * @return void
     */
    public function deinitWriter();

    /**
     * Stores the context named $context with the data $data.
     *
     * $data must contain the translations data map.
     * This method stores the context that it received to the backend specified
     * storage place.
     *
     * @throws TranslationException when the writer is not initialized with
     *                              initWriter().
     * @param string $context
     * @param array  $data
     * @return void
     */
    public function storeContext( $context, array $data );
}
?>
