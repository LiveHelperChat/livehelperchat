<?php
/**
 * File containing the ezcTemplateArray class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * This class contains a bundle of static functions, each implementing a specific
 * function used inside the template language. 
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateArray
{
    /**
     * Returns an array with the values $v1, [$v2, ...] prepended to the original array $array.
     *
     * @param array(mixed) $array
     * @param mixed... $values
     * @return array(mixed)
     */
    public static function array_append( $array, $values )
    {
        $args = func_get_args();
        $array = array_shift( $args );

        return array_merge( $array, $args );
    }

    /**
     * Returns an array with the values $v1, [$v2, ...] prepended to the original array $array.
     *
     * @param array(mixed) $array
     * @param mixed... $values
     * @return array(mixed)
     */
    public static function array_prepend( $array, $values )
    {
        $args = func_get_args();
        $array = array_shift( $args );

        return array_merge( $args, $array );
    }

    /**
     * Returns the given array with two elements: $index1 and $index2 swapped. 
     *
     * @param array(mixed) $array
     * @param int $index1
     * @param int $index2
     * @return array(mixed)
     */
    public static function array_swap( $array, $index1, $index2 )
    {
        $val = $array[$index1];
        $array[$index1] = $array[$index2];
        $array[$index2] = $val;

        unset( $val );
        return $array;
    }

    // array_find_replace( $a, $v, $vNew )::
    // $key = array_search( $v, $a ); if ( $key ) $a[$key] = $vNew;

    /**
     * Searches in the $array for the array key(s) $find and 
     * replaces the value with $replace.
     *
     * @param array(mixed) $array
     * @param string $find
     * @param mixed $replace
     * @return array(mixed)
     */
    public static function array_find_replace( $array, $find, $replace )
    {
        $keys = array_keys( $array, $find ); 

        foreach ( $keys as $key )
        {
            $array[$key] = $replace;
        }

        return $array;
    }


    /** 
     * Returns an array of extracted properties from an array of objects. 
     * The properties named in the array $array. Each property becomes a new value in the resulting array.
     *
     * <code>
     * {use $productsArray}
     * {var $priceArray = array_extract_by_properties( $productsArray, array( "price" ) )}
     * {debug_dump( $priceArray )}
     * </code>
     *
     * The first line of the code above imports an array with product objects. 
     * A product object has at least one property price. Meaning that:
     *
     * <code>
     * {$productArray[0]->price}
     * </code>
     *
     * returns the price of the first product in the array. 
     * The function array_extract_by_properties goes through the whole array of products and stores the 
     * price in the array. The output can be something like:
     *
     * <code>
     * array
     * (
     *     [0] => 200
     *     [1] => 199.24
     *     [2] => 50.20
     * )
     * </code>
     *
     * @param array(mixed) $array
     * @param array(string) $properties
     * @return array(mixed)
     */
    public static function array_extract_by_properties( $array, $properties )
    {
        $list = array();

        foreach ( $array as $item )
        {
            foreach ( $properties as $property )
            {
                $list[] = $item->$property;
            }
        }

        return $list;
    }
 
    /**
     * Returns an array that contains $length times the input $array.
     *
     * @param array(mixed) $array
     * @param int $length
     * @return array(mixed)
     */
    public static function array_repeat( $array, $length )
    {
        $out = array(); 
        for( $i = 0; $i < $length; ++$i)
        {
            $out = array_merge( $out, $array );
        }

        return $out;
    }
  
    /**
     * Returns a sorted array.
     *
     * @param array(mixed) $array
     * @param int $flags            Contains a PHP array sort flag. Default set to SORT_REGULAR.
     * @return array(mixed)
     */
    public static function array_sort( $array, $flags = SORT_REGULAR )
    {
        $tmp = $array;
        sort( $tmp, $flags );
        return $tmp;
    }

    /**
     * Returns a reversed sorted array.
     *
     * @param array(mixed) $array
     * @param int $flags            Contains a PHP array sort flag. Default set to SORT_REGULAR.
     * @return array(mixed)
     */
    public static function array_sort_reverse( $array, $flags = SORT_REGULAR )
    {
        $tmp = $array;
        rsort( $tmp, $flags );
        return $tmp;
    }

    /**
     * Returns a sorted hash, sorted on the values.
     *
     * @param array(mixed) $array
     * @param int $flags            Contains a PHP array sort flag. Default set to SORT_REGULAR.
     * @return array(mixed)
     */
    public static function hash_sort( $array, $flags = SORT_REGULAR )
    {
        $tmp = $array;
        asort( $tmp, $flags );
        return $tmp;
    }

    /**
     * Returns a reversed sorted hash, sorted on the values.
     *
     * @param array(mixed) $array
     * @param int $flags            Contains a PHP array sort flag. Default set to SORT_REGULAR.
     * @return array(mixed)
     */
    public static function hash_sort_reverse( $array, $flags = SORT_REGULAR )
    {
        $tmp = $array;
        arsort( $tmp, $flags );
        return $tmp;
    }

    /**
     * Returns a sorted hash, sorted on the keys.
     *
     * @param array(mixed) $array
     * @param int $flags            Contains a PHP array sort flag. Default set to SORT_REGULAR.
     * @return array(mixed)
     */
    public static function hash_sort_keys( $array, $flags = SORT_REGULAR )
    {
        $tmp = $array;
        ksort( $tmp, $flags );
        return $tmp;
    }

    /**
     * Returns a reversed sorted hash, sorted on the keys.
     *
     * @param array(mixed) $array
     * @param int $flags            Contains a PHP array sort flag. Default set to SORT_REGULAR.
     * @return array(mixed)
     */
    public static function hash_sort_keys_reverse( $array, $flags = SORT_REGULAR )
    {
        $tmp = $array;
        krsort( $tmp, $flags );
        return $tmp;
    }
}


?>
