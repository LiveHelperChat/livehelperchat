<?php
/**
 * File containing the abstract ezcArchiveFileType class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * This class provides methods to detect various archive or compression types.
 *
 * The archive formats that can be detected are:
 * - tar ( v7, ustar, pax, gnu )
 * - zip
 *
 * The compression types that can be detected are:
 * - gzip
 * - bzip2
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
class ezcArchiveFileType
{
    /**
     * Returns the archive type of the given file.
     *
     * @param string $fileName  Absolute or relative path to the file.
     *
     * @return int    Possible values are: {@link ezcArchive::ZIP},
     *               {@link ezcArchive::TAR}, {@link ezcArchive::TAR_V7}, {@link ezcArchive::TAR_USTAR},
     *               {@link ezcArchive::TAR_PAX}, {@link ezcArchive::TAR_GNU}.
     */
    public static function detect( $fileName )
    {
        $fp = fopen( $fileName, "r" );
        if ( $fp === false )
        {
            return false;
        }

        $data = fread( $fp, 512 );
        fclose( $fp );

        if ( self::isGzip( $data ) )
        {
            return ezcArchive::GZIP;
        }

        if ( self::isBzip2( $data ) )
        {
            return ezcArchive::BZIP2;
        }

        if ( self::isZip( $data ) )
        {
            return ezcArchive::ZIP;
        }

        // Order is important.
        if ( self::isPaxTar( $data ) )
        {
            return ezcArchive::TAR_PAX;
        }

        if ( self::isV7Tar( $data ) )
        {
            return ezcArchive::TAR_V7;
        }

        if ( self::isUstarTar( $data ) )
        {
            return ezcArchive::TAR_USTAR;
        }

        if ( self::isGnuTar( $data ) )
        {
            return ezcArchive::TAR_GNU;
        }

        return false;
    }

    /**
     * Checks if the provided $data matches the known signature of a Bzip2 archive.
     *
     * The $data string parameter must contain the first block of data from a file. It will be
     *                     matched against the known signature for this archive type.
     *
     * @param string $data
     * @return bool
     */
    public static function isBzip2( $data )
    {
        if ( ord( $data[0] ) == 0x42 && ord( $data[1] ) == 0x5a )
        {
            return true;
        }
        return false;
    }

    /**
     * Checks if the provided $data matches the known signature of a Gzip archive.
     *
     * The $data string parameter must contain the first block of data from a file. It will be
     *                     matched against the known signature for this archive type.
     *
     * @param string $data
     * @return bool
     */
    public static function isGzip( $data )
    {
        $h = unpack( "Cid1/Cid2", $data[0] . $data[1] );
        if ( $h["id1"] == 0x1f && $h["id2"] == 0x8b )
        {
            return true;
        }

        return false;
    }

    /**
     * Checks if the provided $data matches the known signature of a Zip archive.
     *
     * The $data string parameter must contain the first block of data from a file. It will be
     *                     matched against the known signature for this archive type.
     *
     * @param string $data
     * @return bool
     */
    public static function isZip( $data )
    {
        if ( $data[0] . $data[1] == pack( "v",  0x4b50 ) )
        {
            return true;
        }

        return false;
    }

    /**
     * Checks if the provided $data matches the known signature of a V7 tar archive.
     *
     * The $data string parameter must contain the first block of data from a file. It will be
     *                     matched against the known signature for this archive type.
     *
     * @param string $data
     * @return bool
     */
     public static function isV7Tar( $data )
     {
        if ( strcmp( substr( $data, 257, 5 ), "\0\0\0\0\0" ) == 0 && strcmp( substr( $data, 263, 2 ), "\0\0" ) == 0 )
        {
            if ( self::tarContainsFileName( $data ) )
            {
                return true;
            }
        }

        return false;
     }

    /**
     * Checks if the provided $data matches the known signature of a USTar archive.
     *
     * The $data string parameter must contain the first block of data from a file. It will be
     *                     matched against the known signature for this archive type.
     *
     * @param string $data
     * @return bool
     */
    public static function isUstarTar( $data )
    {
        if ( strcmp( substr( $data, 257, 5 ), "ustar" ) == 0 && strcmp( substr( $data, 263, 2 ), "00" ) == 0 )
        {
            if ( self::tarContainsFileName( $data ) )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the provided $data matches the known signature of a PAX tar archive.
     *
     * The $data string parameter must contain the first block of data from a file. It will be
     *                     matched against the known signature for this archive type.
     *
     * @param string $data
     * @return bool
     */
    public static function isPaxTar( $data )
    {
        if ( strcmp( substr( $data, 257, 5 ), "ustar" ) == 0 && strcmp( substr( $data, 263, 2 ), "00" ) == 0 )
        {
            $type = substr( $data, 156, 1 );
            if ( $type == "x" || $type == "g" )
            {
                if ( self::tarContainsFileName( $data ) )
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Checks if the provided $data matches the known signature of a GNU tar archive.
     *
     * The $data string parameter must contain the first block of data from a file. It will be
     *                     matched against the known signature for this archive type.
     *
     * @param string $data
     * @return bool
     */
    public static function isGnuTar( $data )
    {
        if ( strcmp( substr( $data, 257, 5 ), "ustar" ) == 0 && strcmp( substr( $data, 263, 2 ), " \0" ) == 0 )
        {
            if ( self::tarContainsFileName( $data ) )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the provided $data contains a file name.
     *
     * The $data string parameter must contain the first block of data from a file. It will be
     *                     matched against the known signature for this archive type.
     *
     * @param string $data
     * @return bool
     */
    public static function tarContainsFileName( $data )
    {
        return $data[0] != "\0";
    }
}
?>
