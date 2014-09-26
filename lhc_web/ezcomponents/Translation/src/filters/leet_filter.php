<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * Implements the Leet translation filter.
 *
 * The leet filter mangles translations to old skool 1337 73x7.
 *
 * @package Translation
 * @version 1.3.2
 */
class ezcTranslationLeetFilter implements ezcTranslationFilter
{
    /**
     * @param ezcTranslationLeetFilter Instance
     */
    static private $instance = null;

    /**
     * Private constructor to prevent non-singleton use
     */
    private function __construct()
    {
    }

    /**
     * Returns an instance of the class ezcTranslationFilterLeet
     *
     * @return ezcTranslationFilterLeet Instance of ezcTranslationFilterLeet
     */
    public static function getInstance()
    { 
        if ( is_null( self::$instance ) ) 
        { 
            self::$instance = new ezcTranslationLeetFilter(); 
        } 
        return self::$instance; 
    }

    /**
     * This "leetify" the $text.
     *
     * @param string $text
     * @return string
     */
    static private function leetify( $text )
    {
        $searchMap = array( '/to/i', '/for/i', '/ate/i', '/your/i', '/you/i', '/l/i', '/e/i', '/o/i', '/a/i', '/t/i' );
        $replaceMap = array( '2', '4', '8', 'ur', 'u', '1', '3', '0', '4', '7' );

        $textBlocks = preg_split( '/(%[^ ]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE );
        $newTextBlocks = array();
        foreach ( $textBlocks as $text )
        {
            if ( strlen( $text ) && $text[0] == '%' )
            {
                $newTextBlocks[] = (string) $text;
                continue;
            }
            $text = preg_replace( $searchMap, $replaceMap, $text );

            $newTextBlocks[] = (string) $text;
        }
        $text = implode( '', $newTextBlocks );
        return $text;
    }

    /**
     * Filters a context
     *
     * Applies the "1337" filter on the given context. This filter leetifies
     * text old skool. It is, of course, just an example.
     *
     * @param array[ezcTranslationData] $context
     * @return void
     */
    public function runFilter( array $context )
    {
        foreach ( $context as $element )
        {
            $element->translation = self::leetify( $element->translation );
        }
    }
}
?>
