<?php
/**
 * File containing the ezcArchiveChecksums class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcArchiveChecksums is a collection of checksum algorithms. The
 * total-byte-value checksum and CRC32 checksum are currently available.
 *
 * For each different checksum are two methods available:
 * - Calculate the checksum from a string.
 * - Calculate the checksum from a file.
 *
 * The latter will consume less memory since a part of the file is read in
 * memory at the time, and will be freed after use. The consequence might be
 * that the checksum from a string is a bit faster.
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
class ezcArchiveChecksums
{
    /**
     * The CRC-32 lookup table.
     *
     * @var array(int)
     */
    private static $crc32Table = false;

    /**
     * Calculates the total-byte-value checksum from a string.
     *
     * Returns the total ASCII value from all characters in the data string.
     * Example 1: For example:
     *
     * <code>
     * $crc = ezcArchiveChecksums::getTotalByteValueFromFile( "abc" );
     * // $crc contains the value: 141 + 142 + 143 = 426
     * </code>
     *
     * @param string $data  Character string.
     * @return int The total byte value.
     *
     * @see getTotalByteValueFromFile()
     */
    public static function getTotalByteValueFromString( $data )
    {
        $total = 0;
        $length = strlen( $data );
        for ( $i = 0; $i < $length; $i++ )
        {
            $total += ord( $data[$i] );
        }

        return $total;
    }

    /**
     * Calculates the total-byte-value checksum from a file.
     *
     * Returns the total ASCII value from all characters in the data string.
     * For example:
     *
     * <code>
     * $crc = ezcArchiveChecksums::getTotalByteValueFromFile( "abc" );
     * // $crc contains the value: 141 + 142 + 143 = 426
     * </code>
     *
     * @param string $fileName The file to use
     * @return int Value which contains the total byte value.
     */
    public static function getTotalByteValueFromFile( $fileName )
    {
        if ( ( $fp = fopen( $fileName, 'rb' ) ) === false )
        {
            return false;
        }

        // Perform the algorithm on each character in file
        $total = 0;
        while ( true )
        {
            $i = fread( $fp, 1 );
            if ( strlen( $i ) == 0 )
            {
                break;
            }

            $total += ord( $i );
        }

        fclose( $fp );

        return $total;
    }

   /**
    * Calculates the (official) CRC-32 polynomial from a $data string as input.
    *
    * @param string $data
    * @return int The calculated CRC-32.
    */
    public static function getCrc32FromString( $data )
    {
        return crc32( $data );
    }

    /**
     * Calculates the (official) CRC-32 polynomial from a file.
     *
     * This method is taken from the PHP user comments
     * (http://no.php.net/manual/en/function.crc32.php).
     *
     * @param string $fileName Absolute or relative path to the file.
     * @return int The calculated CRC-32.
     */
    public static function getCrc32FromFile( $fileName )
    {
        if ( self::$crc32Table === false )
        {
            self::crc32InitTable();
        }

        // Once the lookup table has been filled in by the two functions above,
        // this function creates all CRCs using only the lookup table.

        // You need unsigned variables because negative values
        // introduce high bits where zero bits are required.
        // PHP doesn't have unsigned integers:
        // I've solved this problem by doing a '&' after a '>>'.

        // Start out with all bits set high.
        $crc = 0xffffffff;

        // Added for issue #13517: Not possible to add directories to an archive on Windows
        if ( is_dir( $fileName ) )
        {
            return false;
        }

        if ( ( $fp = fopen( $fileName ,'rb' ) ) === false )
        {
            return false;
        }

        // Perform the algorithm on each character in file
        while ( true )
        {
            $i = fread( $fp, 1 );
            if ( strlen( $i ) == 0 )
            {
                break;
            }
            $crc = ( ( $crc >> 8 ) & 0x00ffffff ) ^ self::$crc32Table[( $crc & 0xFF ) ^ ord( $i )];
        }

        fclose( $fp );

        // Exclusive OR the result with the beginning value.
        return $crc ^ 0xffffffff;
    }

    /**
     * Initializes the CRC-32 table.
     *
     * Builds the lookup table array. This is the official polynomial used by
     * CRC-32 in PKZip, WinZip and Ethernet.
     *
     * This method is taken from the PHP user comments
     * (http://no.php.net/manual/en/function.crc32.php).
     */
    protected static function crc32InitTable()
    {
        // Builds lookup table array
        // This is the official polynomial used by
        // CRC-32 in PKZip, WinZip and Ethernet.
        $polynomial = 0x04c11db7;

        // 256 values representing ASCII character codes.
        for ( $i = 0; $i <= 0xFF; ++$i )
        {
            self::$crc32Table[$i] = ( self::crc32Reflect( $i, 8 ) << 24 );

            for ( $j = 0; $j < 8; ++$j )
            {
                self::$crc32Table[$i] = ( ( self::$crc32Table[$i] << 1 ) ^ ( ( self::$crc32Table[$i] & ( 1 << 31 ) ) ? $polynomial : 0 ) );
            }

            self::$crc32Table[$i] = self::crc32Reflect( self::$crc32Table[$i], 32 );
        }
    }

    /**
     * Reflects CRC bits in the lookup table
     *
     * This method is taken from the PHP user comments
     * (http://no.php.net/manual/en/function.crc32.php).
     *
     * @param int $ref
     * @param int $ch
     * @return int
     */
    protected static function crc32Reflect( $ref, $ch )
    {
        $value = 0;

        // Swap bit 0 for bit 7, bit 1 for bit 6, etc.
        for ( $i = 1; $i < ( $ch + 1 ); ++$i )
        {
            if ( $ref & 1 )
            {
                $value |= ( 1 << ( $ch - $i ) );
            }

            $ref = ( ( $ref >> 1 ) & 0x7fffffff );
        }

        return $value;
    }
}
?>
