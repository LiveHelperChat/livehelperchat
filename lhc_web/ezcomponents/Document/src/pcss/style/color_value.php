<?php
/**
 * File containing the ezcDocumentPcssStyleColorValue class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style directive color value representation.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPcssStyleColorValue extends ezcDocumentPcssStyleValue
{
    /**
     * Sub regular expression for short hexadecimal color notation.
     * 
     * @var string
     */
    protected $shortHexNotation = '(?:#?([0-9a-f])([0-9a-f])([0-9a-f])([0-9a-f])?)';

    /**
     * Sub regular expression for long hexadecimal color notation.
     * 
     * @var string
     */
    protected $longHexNotation = '(?:#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})?)';

    /**
     * Sub regular expression for rgb() color notation.
     * 
     * @var string
     */
    protected $rgbSpec = '(?:\s*rgb\s*\(\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*\)\s*)';

    /**
     * Sub regular expression for rgba() color notation.
     * 
     * @var string
     */
    protected $rgbaSpec = '(?:\s*rgba\s*\(\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\s*\)\s*)';

    /**
     * Parse value string representation
     *
     * Parse the string representation of the value into a usable
     * representation.
     * 
     * @param string $value 
     */
    public function parse( $value )
    {
        switch ( true )
        {
            // Sepcial values
            case ( trim( $value ) === 'transparent' ) ||
                 ( trim( $value ) === 'none' ):
                $this->value = array(
                    'red'   => 0.,
                    'green' => 0.,
                    'blue'  => 0.,
                    'alpha' => 1.,
                );
                break;

            // Match 12 and 16bit hex value color definitions
            case preg_match( '(^' . $this->shortHexNotation . '$)USi', $value, $match ):
                $this->value = array(
                    'red'   => hexdec( $match[1] ) / 15,
                    'green' => hexdec( $match[2] ) / 15,
                    'blue'  => hexdec( $match[3] ) / 15,
                    'alpha' => isset( $match[4] ) ? hexdec( $match[4] ) / 15 : 0.,
                );
                break;

            // Match 24 and 32bit hex value color definitions
            case preg_match( '(^' . $this->longHexNotation . '$)Ui', $value, $match ):
                $this->value = array(
                    'red'   => hexdec( $match[1] ) / 255,
                    'green' => hexdec( $match[2] ) / 255,
                    'blue'  => hexdec( $match[3] ) / 255,
                    'alpha' => isset( $match[4] ) ? hexdec( $match[4] ) / 255 : 0.,
                );
                break;

            // Match RGB array specification
            case preg_match( '(^' . $this->rgbSpec . '$)Si', $value, $match ):
                $this->value = array(
                    'red'   => $match[1] % 256 / 255,
                    'green' => $match[2] % 256 / 255,
                    'blue'  => $match[3] % 256 / 255,
                    'alpha' => 0,
                );
                break;

            // Match RGBA array specification
            case preg_match( '(^' . $this->rgbaSpec . '$)Si', $value, $match ):
                $this->value = array(
                    'red'   => $match[1] % 256 / 255,
                    'green' => $match[2] % 256 / 255,
                    'blue'  => $match[3] % 256 / 255,
                    'alpha' => $match[4] % 256 / 255,
                );
                break;

            default:
                throw new ezcDocumentParserException( E_PARSE, "Invalid color specification: " . $value );
        }

        return $this;
    }

    /**
     * Get regular expression matching the value.
     *
     * Return a regular sub expression, which matches all possible values of
     * this value type. The regular expression should NOT contain any named
     * sub-patterns, since it might be repeatedly embedded in some box parser.
     * 
     * @return string
     */
    public function getRegularExpression()
    {
        return '(?i:transparent|none|' .
            $this->shortHexNotation . '|' .
            $this->longHexNotation . '|' .
            $this->rgbSpec . '|' .
            $this->rgbaSpec . ')';
    }

    /**
     * Convert value to string.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf( '#%02x%02x%02x%s',
            $this->value['red'] * 255,
            $this->value['green'] * 255,
            $this->value['blue'] * 255,
            $this->value['alpha'] > 0 ? sprintf( '%02x', $this->value['alpha'] * 255 ) : ''
        );
    }
}

?>
