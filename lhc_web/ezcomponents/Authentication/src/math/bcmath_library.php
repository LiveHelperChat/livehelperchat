<?php
/**
 * File containing the ezcAuthenticationBcmathLibrary class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Wrapper class for the PHP bcmath extension.
 *
 * @package Authentication
 * @version 1.3.1
 * @access private
 */
class ezcAuthenticationBcmathLibrary extends ezcAuthenticationBignumLibrary
{
    /**
     * Creates a new big number from $number in the base $base.
     *
     * In the PHP extension bcmath the numbers are used as strings, not as
     * strings, so this function returns the provided $number as it is, and
     * without transforming it in the base $base.
     *
     * @param string $number The number from which to create the result
     * @param int $base The base in which the result will be
     * @return string
     */
    public function init( $number, $base = 10 )
    {
        return $number;
    }

    /**
     * Adds two numbers.
     *
     * @param string $a The first number
     * @param string $b The second number
     * @return string
     */
    public function add( $a, $b )
    {
        return bcadd( $a, $b );
    }

    /**
     * Substracts two numbers.
     *
     * @param string $a The first number
     * @param string $b The second number
     * @return string
     */
    public function sub( $a, $b )
    {
        return bcsub( $a, $b );
    }

    /**
     * Multiplies two numbers.
     *
     * @param string $a The first number
     * @param string $b The second number
     * @return string
     */
    public function mul( $a, $b )
    {
        return bcmul( $a, $b );
    }

    /**
     * Divides two numbers.
     *
     * @param string $a The first number
     * @param string $b The second number
     * @return string
     */
    public function div( $a, $b )
    {
        return bcdiv( $a, $b, 0 );
    }

    /**
     * Computes $base modulo $modulus.
     *
     * @param string $base The number to apply modulo to
     * @param string $modulus The modulo value to be applied to $base
     * @return string
     */
    public function mod( $base, $modulus )
    {
        return bcmod( $base, $modulus );
    }

    /**
     * Computes $base to the power of $exponent.
     *
     * @param string $base The number to be exponentiated
     * @param string $exponent The exponent to apply to $base
     * @return string
     */
    public function pow( $base, $exponent )
    {
        return bcpow( $base, $exponent );
    }

    /**
     * Computes $base to the power of $exponent and then applies modulo $modulus.
     *
     * @param string $base The number to be exponentiated
     * @param string $exponent The exponent to apply to $base
     * @param string $modulus The modulo value to be applied to the result
     * @return string
     */
    public function powmod( $base, $exponent, $modulus )
    {
        return bcpowmod( $base, $exponent, $modulus );
    }

    /**
     * Computes the inverse of $number in modulo $modulus.
     *
     * @param string $number The number for which to calculate the inverse
     * @param string $modulus The modulo value in which the inverse is calculated
     * @return string
     */
    public function invert( $number, $modulus )
    {
        while ( bccomp( $number, 0 ) < 0 )
        { 
            $number = bcadd( $number, $modulus );
        }
        $r = $this->gcd( $number, $modulus );
        if ( (int)$r[2] === 1 )
        {
            $a = $r[0];
            while ( bccomp( $a, 0 ) < 0 )
            {
                $a = bcadd( $a, $modulus );
            }
            return $a;
        }
        else
        {
            return false;
        }
    }

    /**
     * Finds the greatest common denominator of two numbers using the extended
     * Euclidean algorithm.
     *
     * The returned array is ( x, y, gcd( a, b ) ), where
     *     x * a + y * b = gcd( a, b )
     *
     * @param string $a The first number
     * @param string $b The second number
     * @return array(string)
     */
    public function gcd( $a, $b )
    {
        $x = 0;
        $xLast = 1;

        $y = 1;
        $yLast = 0;

        while ( bccomp( $b, 0 ) !== 0 )
        {
            $temp = $b;
            $q = bcdiv( $a, $b, 0 );
            $b = bcmod( $a, $b );
            $a = $temp;

            $temp = $x;
            $x = bcsub( $xLast, bcmul( $q, $x ) );
            $xLast = $temp;

            $temp = $y;
            $y = bcsub( $yLast, bcmul( $q, $y ) );
            $yLast = $temp;

        }
        return array( $xLast, $yLast, $a );
    }

    /**
     * Compares two numbers.
     *
     * Returns an integer:
     *  - a positive value if $a > $b
     *  - zero if $a == $b
     *  - a negative value if $a < $b
     *
     * @param string $a The first number
     * @param string $b The second number
     * @return int
     */
    public function cmp( $a, $b )
    {
        return bccomp( $a, $b );
    }

    /**
     * Returns the string representation of number $a.
     *
     * @param string $number The number to be represented as a string
     * @return string
     */
    public function toString( $number )
    {
        return $number;
    }
}
?>
