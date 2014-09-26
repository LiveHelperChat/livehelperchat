<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * Implements the ComplementEmpty translation filter.
 *
 * The filter replaces a missing translated string with its original.
 *
 * @package Translation
 * @version 1.3.2
 * @mainclass
 */
class ezcTranslationComplementEmptyFilter implements ezcTranslationFilter
{
    /**
     * Singleton instance
     * @var ezcTranslationComplementEmptyFilter
     */
    static private $instance = null;

    /**
     * Private constructor to prevent non-singleton use.
     */
    private function __construct()
    {
    }

    /**
     * Returns an instance of the class ezcTranslationComplementEmptyFilter.
     *
     * @return ezcTranslationComplementEmptyFilter Instance of ezcTranslationComplementEmptyFilter
     */
    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new ezcTranslationComplementEmptyFilter();
        }
        return self::$instance;
    }

    /**
     * Filters the context $context.
     *
     * Applies the fillin filter on the given context. The filter replaces a
     * missing translated string with its original.
     *
     * @param array(ezcTranslationData) $context
     * @return void
     */
    public function runFilter( array $context )
    {
        foreach ( $context as $element )
        {
            if ( $element->status == ezcTranslationData::UNFINISHED )
            {
                $element->translation = $element->original;
            }
        }
    }
}
?>
