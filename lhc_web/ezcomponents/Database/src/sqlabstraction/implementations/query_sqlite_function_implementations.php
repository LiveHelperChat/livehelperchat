<?php
/**
 * File containing the ezcQueryExpressionSqlite class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcQueryExpressionSqlite class is used to create SQL expression for SQLite.
 *
 * This class reimplements the methods that have a different syntax in
 * SQLite (substr) and contains PHP implementations of functions that are
 * registered with SQLite with it's PDO::sqliteRegisterFunction() method.
 *
 * @package Database
 * @version 1.4.7
 */
class ezcQuerySqliteFunctions
{
    /**
     * Returns the md5 sum of the data that SQLite's md5() function receives.
     *
     * @param string $data
     * @return string
     */
    static public function md5Impl( $data )
    {
        return md5( $data );
    }

    /**
     * Returns the modules of the data that SQLite's mod() function receives.
     *
     * @param numeric $dividend
     * @param numeric $divisor
     * @return string
     */
    static public function modImpl( $dividend, $divisor )
    {
        return $dividend % $divisor;
    }

    /**
     * Returns a concattenation of the data that SQLite's concat() function receives.
     *
     * @return string
     */
    static public function concatImpl()
    {
        $args = func_get_args();
        return join( '', $args );
    }

    /**
     * Returns the SQL to locate the position of the first occurrence of a substring
     * 
     * @param string $substr
     * @param string $value
     * @return integer
     */
     static public function positionImpl( $substr, $value )
     {
         return strpos( $value, $substr ) + 1;
     }

    /**
     * Returns the next lowest integer value from the number
     * 
     * @param numeric $number
     * @return integer
     */
     static public function floorImpl( $number )
     {
         return (int) floor( $number );
     }

     /**
      * Returns the next highest integer value from the number
      * 
      * @param numeric $number
      * @return integer
      */
     static public function ceilImpl( $number )
     {
         return (int) ceil( $number );
     }

     /**
      * Returns the unix timestamp belonging to a date/time spec
      *
      * @param string $spec
      * @return integer
      */
     static public function toUnixTimestampImpl( $spec )
     {
         return strtotime( $spec );
     }
}
?>
