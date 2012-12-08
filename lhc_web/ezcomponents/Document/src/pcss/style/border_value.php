<?php
/**
 * File containing the ezcDocumentPcssStyleBorderValue class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Style directive border value representation.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPcssStyleBorderValue extends ezcDocumentPcssStyleValue
{
    /**
     * Default value.
     * 
     * @var array
     */
    protected $defaultValue = array(
        'width' => 0,
        'style'  => 'solid',
        'color' => array(
            'red'   => 1,
            'green' => 1,
            'blue'  => 1,
            'alpha' => 0,
        )
    );

    /**
     * Construct value.
     *
     * Optionally pass a parsed representation of the value.
     * 
     * @param mixed $value 
     */
    public function __construct( $value = null )
    {
        parent::__construct( $value === null ? $this->defaultValue : $value );
    }

    /**
     * Parse value string representation.
     *
     * Parse the string representation of the value into a usable
     * representation.
     * 
     * @param string $value 
     * @return ezcDocumentPcssStyleValue
     */
    public function parse( $value )
    {
        $widthParser = new ezcDocumentPcssStyleMeasureValue();
        $styleParser  = new ezcDocumentPcssStyleLineValue();
        $colorParser = new ezcDocumentPcssStyleColorValue();

        $regexp = '(^\s*' .
            '(?:(?P<width>' . $widthParser->getRegularExpression() . ')\s*)?' .
            '(?:(?P<style>'  . $styleParser->getRegularExpression()  . ')\s*)?' .
            '(?:(?P<color>' . $colorParser->getRegularExpression() . ')\s*)?' .
        '\s*$)';

        if ( !preg_match( $regexp, $value, $match ) )
        {
            throw new ezcDocumentParserException( E_PARSE, "Invalid border specification: " . $value );
        }

        $this->value = $this->defaultValue;
        if ( isset( $match['width'] ) && !empty( $match['width'] ) )
        {
            $this->value['width'] = $widthParser->parse( $match['width'] )->value;
        }

        if ( isset( $match['style'] ) && !empty( $match['style'] ) )
        {
            $this->value['style'] = $styleParser->parse( $match['style'] )->value;
        }

        if ( isset( $match['color'] ) && !empty( $match['color'] ) )
        {
            $this->value['color'] = $colorParser->parse( $match['color'] )->value;
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
        $widthParser = new ezcDocumentPcssStyleMeasureValue();
        $styleParser  = new ezcDocumentPcssStyleLineValue();
        $colorParser = new ezcDocumentPcssStyleColorValue();

        return '(?:' .
            '(?:' . $widthParser->getRegularExpression() . '\s*)?' .
            '(?:'  . $styleParser->getRegularExpression()  . '\s*)?' .
            '(?:' . $colorParser->getRegularExpression() . ')?' .
        ')';
    }

    /**
     * Convert value to string.
     *
     * @return string
     */
    public function __toString()
    {
        return 
            new ezcDocumentPcssStyleMeasureValue( $this->value['width'] ) . ' ' .
            new ezcDocumentPcssStyleLineValue( $this->value['style'] ) . ' ' .
            new ezcDocumentPcssStyleColorValue( $this->value['color'] );
    }
}

?>
