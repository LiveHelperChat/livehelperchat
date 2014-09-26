<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * Interface for Translation backends.
 *
 * This interface describes the methods that a Translation backend should
 * implement.
 *
 * For an example see {@link ezcTranslationTsBackend}.
 *
 * @package Translation
 * @version 1.3.2
 */
interface ezcTranslationBackend
{
    /**
     * Sets the backend specific $configurationData.
     *
     * $configurationData should be an implementation of ezcBaseOptions (or, for
     * sake of backwards compatibility an associative array). See 
     * {@link ezcTranslationTsBackend} for an example implementation.
     *
     * Each implementor must document the options that it accepts and throw an
     * {@link ezcBaseConfigException} with the
     * {@link ezcBaseConfigException::UNKNOWN_CONFIG_SETTING} type if an option
     * is not supported.
     *
     * @param mixed $configurationData
     * @return void
     */
    public function setOptions( $configurationData );

    /**
     * Returns an array with translation data for the context $context and the locale
     * $locale.
     *
     * This method returns an array describing the map used for translation of text.
     * For the format see {@link ezcTranslation::$translationMap}.
     *
     * @throws TranslationException when a context is not available.
     * @param string $locale
     * @param string $context
     * @return array
     */
    public function getContext( $locale, $context );
}
?>
