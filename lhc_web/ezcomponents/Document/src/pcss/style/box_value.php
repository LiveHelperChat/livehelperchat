<?php
/**
 * File containing the ezcDocumentPcssStyleBoxValue class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Abstract value tpye for box value representations.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
abstract class ezcDocumentPcssStyleBoxValue extends ezcDocumentPcssStyleValue
{
    /**
     * Default value
     * 
     * @var array
     */
    protected $defaultValue = array(
        'top'    => null,
        'right'  => null,
        'bottom' => null,
        'left'   => null,
    );

    /**
     * Construct value
     *
     * Optionally pass a parsed representation of the value.
     * 
     * @param mixed $value 
     * @return void
     */
    public function __construct( $value = null )
    {
        parent::__construct( $value === null ? $this->defaultValue : $value );

        $subValueClass = $this->getSubValue();
        $subValue      = new $subValueClass();
        foreach ( $this->value as $key => $value )
        {
            if ( $value === null )
            {
                $this->value[$key] = $subValue->value;
            }
        }
    }

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
        $subValueClass = $this->getSubValue();
        $subValue      = new $subValueClass();
        $subExpression = $subValue->getRegularExpression();

        // We match the value counts iteratively and not in one singular 
        // regular expression, because older PHP versions cause segmentation 
        // faults with the resulting complex regular expression. Even possible, 
        // it should not be changed back to matching one singular regular 
        // expression.
        for ( $i = 1; $i <= 4; ++$i )
        {
            $regexps = array();
            for ( $j = 1; $j <= $i; ++$j )
            {
                $regexps[] = "(?P<m$j>$subExpression)";
            }

            if ( !preg_match( "(^" . implode( '\\s+', $regexps ) . "$)", $value, $match ) ) 
            {
                continue;
            }

            switch ( $i )
            {
                case 1:
                    $this->value = array(
                        'top'    => $subValue->parse( $match['m1'] )->value,
                        'right'  => $subValue->parse( $match['m1'] )->value,
                        'bottom' => $subValue->parse( $match['m1'] )->value,
                        'left'   => $subValue->parse( $match['m1'] )->value,
                    );
                    return $this;

                case 2:
                    $this->value = array(
                        'top'    => $subValue->parse( $match['m1'] )->value,
                        'right'  => $subValue->parse( $match['m2'] )->value,
                        'bottom' => $subValue->parse( $match['m1'] )->value,
                        'left'   => $subValue->parse( $match['m2'] )->value,
                    );
                    return $this;

                case 3:
                    $this->value = array(
                        'top'    => $subValue->parse( $match['m1'] )->value,
                        'right'  => $subValue->parse( $match['m2'] )->value,
                        'bottom' => $subValue->parse( $match['m3'] )->value,
                        'left'   => $subValue->parse( $match['m2'] )->value,
                    );
                    return $this;

                case 4:
                    $this->value = array(
                        'top'    => $subValue->parse( $match['m1'] )->value,
                        'right'  => $subValue->parse( $match['m2'] )->value,
                        'bottom' => $subValue->parse( $match['m3'] )->value,
                        'left'   => $subValue->parse( $match['m4'] )->value,
                    );
                    return $this;
            }
        }

        throw new ezcDocumentParserException( E_PARSE, "Invalid number of elements in measure box specification: $value" );
    }

    /**
     * Get sub value handler class name
     * 
     * @return string
     */
    abstract protected function getSubValue();

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
        // Embedding a boxed measure would lead to totally npredictable
        // results, so we just return null.
        return null;
    }

    /**
     * Convert value to string
     *
     * @return string
     */
    public function __toString()
    {
        $subValueClass = $this->getSubValue();

        return 
            new $subValueClass( $this->value['top'] ) . ' ' .
            new $subValueClass( $this->value['right'] ) . ' ' .
            new $subValueClass( $this->value['bottom'] ) . ' ' .
            new $subValueClass( $this->value['left'] );
    }
}

?>
