<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * ezcTranslationFilter defines the common interface for all translation filters.
 *
 * Example:
 * @see ezcTranslationFilterBork::runFilter().
 *
 * @package Translation
 * @version 1.3.2
 */
interface ezcTranslationFilter
{
    /**
     * Returns an instance of the class that implements this interface
     *
     * @return ezcTranslationFilter
     */
    public static function getInstance();

    /**
     * Filters the context $context.
     *
     * This static method is called by the Translation Manager whenever a
     * context is requested. The method should only modify the "translated"
     * string and not touch the original string or comment.
     *
     * For a definition of the array format see {@link
     * ezcTranslation::$translationMap}.
     *
     * @param array(ezcTranslationData) $context
     * @return void
     */
    public function runFilter( array $context );
}
?>
