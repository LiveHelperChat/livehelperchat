<?php
/**
 * File containing the ezcTemplateTranslationProvider class
 *
 * @package TemplateTranslationTiein
 * @version 1.1.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcTemplateTranslationProvider provides functions that are called from the
 * template compiler to either translate strings, or convert them into code.
 *
 * @package TemplateTranslationTiein
 * @mainclass
 * @version 1.1.1
 */
class ezcTemplateTranslationProvider
{
    /**
     * Translates the string $string from the context $context with $arguments as variables.
     *
     * This static method is called whenever a template directly needs a
     * translated string with the variables substituted.
     *
     * @param string $string
     * @param string $context
     * @param array(string=>mixed) $arguments
     * @return string
     */
    static public function translate( $string, $context, $arguments )
    {
        $ttc = ezcTemplateTranslationConfiguration::getInstance();
        $ctxt = $ttc->manager->getContext( $ttc->locale, $context );
        $translation = $ctxt->getTranslation( $string, $arguments );
        return $translation;
    }

    /**
     * Compiles the string $string from the context $context with $arguments as variables into executable code.
     *
     * This static method translates a string, but inserts special code as
     * replacements for the variables.
     *
     * @param string $string
     * @param string $context
     * @param array(string=>mixed) $arguments
     * @return string
     */
    static public function compile( $string, $context, $arguments )
    {
        $ttc = ezcTemplateTranslationConfiguration::getInstance();
        $ctxt = $ttc->manager->getContext( $ttc->locale, $context );
        $translation = $ctxt->compileTranslation( $string, $arguments );
        return $translation;
    }
}
?>
