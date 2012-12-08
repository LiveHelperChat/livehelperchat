<?php
/**
 * File containing the ezcDocumentPcssStyleSrcValue class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style directive source value representation
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPcssStyleSrcValue extends ezcDocumentPcssStyleValue
{
    /**
     * Parse value string representation
     *
     * Parse the string representation of the value into a usable
     * representation.
     * 
     * @param string $value 
     * @return void
     */
    public function parse( $value )
    {
        $this->value = array();
        $values = preg_split( '(\s*,\s*)', $value );
        foreach( $values as $url )
        {
            if ( preg_match( '(^\s*(?:url|local)\s*\(\s*(?P<url>\S+)\s*\)\s*$)', $url, $match ) )

            {
                $this->value[] = $match['url'];
            }
            else
            {
                throw new ezcDocumentParserException( E_PARSE, "Inavlid URL definition: " . $url );
            }
        }

        return $this;
    }
    
    /**
     * Get regular expression matching the value
     *
     * Return a regular sub expression, which matches all possible values of
     * this value type. The regular expression should NOT contain any named
     * sub-patterns, since it might be repeatedly embedded in some box parser.
     * 
     * @return string
     */
    public function getRegularExpression()
    {
        return '.*';
    }

    /**
     * Convert value to string
     *
     * @return string
     */
    public function __toString()
    {
        $urls = array();
        foreach ( $this->value as $url )
        {
            $urls[] = "url( $url )";
        }

        return implode( ', ', $urls );
    }
}

?>
