<?php
/**
 * File containing the ezcArchiveLocalFileHeader class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcArchiveLocalFileHeader class represents the Zip local header.
 *
 * ezcArchiveLocalFileHeader can read the header from an ezcArchiveCharacterFile or ezcArchiveEntry.
 *
 * The values from the headers are directly accessible via the class properties, and allows
 * reading and writing to specific header values.
 *
 * The entire header can be appended to an ezcArchiveCharacterFile again or written to an ezcArchiveFileStructure.
 * Information may get lost, though.
 *
 * The overall .ZIP file format[1] :
 *
 * <pre>
 *   [local file header 1]
 *   [file data 1]
 *   [data descriptor 1]
 *   .
 *   .
 *   [local file header n]
 *   [file data n]
 *   [data descriptor n]
 *   [central directory]
 *   [end of central directory record]
 * </pre>
 *
 * The Local File Header has the following structure:
 *
 * <pre>
 * + ---+--------+------------+-------------------+-------------------------------+
 * | ID | Offset | Field size | Property          |  Description                  |
 * +----+--------+------------+-------------------+-------------------------------+
 * |    |  0     | 4          | -                 | Local file header signature   |
 * |    |  4     | 2          | version           | Version needed to extract     |
 * |    |  6     | 2          | bitFlag           | General purpose bit flag      |
 * |    |  8     | 2          | compressionMethod | Compression method            |
 * |    |  10    | 2          | lastModFileTime   | Last modification file time   |
 * |    |  12    | 2          | lastModFileDate   | Last modification file date   |
 * |    |  14    | 4          | crc               | crc-32                        |
 * |    |  18    | 4          | compressedSize    | compressed size               |
 * |    |  22    | 4          | uncompressedSize  | uncompressed size             |
 * | X  |  26    | 2          | fileNameLength    | file name length              |
 * | Y  |  28    | 2          | extraFieldLength  | extra field length            |
 * |    |  30    | X          | fileName          | file name                     |
 * |    |  30+X  | Y          | -                 | extra field                   |
 * +----+--------+------------+-------------------+-------------------------------+
 * </pre>
 *
 * The columns of the table are:
 * - ID gives a label to a specific field or row in the table.
 * - Offset describes the start position of a header field.
 * - Field size describes the size of the field in bytes.
 * - Property is the name of the property that will be set by the header field.
 * - Description explains what this field describes.
 *
 * The local file signature cannot be changed and is set in the constant {@link self::magic}.
 *
 * The extra fields that are implemented are:
 * - Info-Zip Unix field (old).
 * - Info-Zip Unix field (new).
 * - Universal timestamp.
 *
 * Info-Zip Unix field (old):
 * <pre>
 * +--------+------------+------------------------------------+
 * | Offset | Field size |  Description                       |
 * +--------+------------+------------------------------------+
 * |  0     | 2          | Header ID (0x5855)                 |
 * |  2     | 2          | Data size: Full size = 12          |
 * |  4     | 4          | Last Access Time                   |
 * |  8     | 4          | Last Modification Time             |
 * |  12    | 2          | User ID  (Optional if size < 12 )  |
 * |  14    | 2          | Group ID (Optional if size < 12 )  |
 * +--------+------------+------------------------------------+
 * </pre>
 *
 * Info-Zip Unix field (new):
 * <pre>
 * +--------+------------+--------------------+
 * | Offset | Field size |  Description       |
 * +--------+------------+--------------------+
 * |  0     | 2          | Header ID (0x7855) |
 * |  2     | 2          | Data size.         |
 * |  4     | 2          | User ID            |
 * |  6     | 2          | Group ID           |
 * +--------+------------+--------------------+
 * </pre>
 *
 * Universal timestamp:
 * <pre>
 * +--------+------------+-------------------------+
 * | Offset | Field size |  Description            |
 * +--------+------------+-------------------------+
 * |  0     | 2          | Header ID (0x5455)      |
 * |  2     | 2          | Data size.              |
 * |  4     | 1          | Info bits (flags)       |
 * |  5     | 4          | Last Modification Time  |
 * |  9     | 4          | Last Access Time        |
 * |  13    | 4          | Creation Time           |
 * +--------+------------+-------------------------+
 * </pre>
 *
 * Info bits indicate which fields are set:
 * - bit 0, if set then the modification time is present.
 * - bit 1, if set then the access time is present.
 * - bit 2, if set then the creation time is present.
 *
 * These info bits are ONLY valid for the local file header. The
 * central header has the same Universal Timestamp, but reflects
 * the bits from the local file header!
 *
 *
 * See PKWare documentation and InfoZip extra field documentation for more information.
 *
 *
 * [1] PKWARE .ZIP file format specification:
 * <pre>
 * File:    APPNOTE.TXT - .ZIP File Format Specification
 * Version: 4.5
 * Revised: 11/01/2001
 * Copyright (c) 1989 - 2001 PKWARE Inc., All Rights Reserved.
 * </pre>
 *
 * [2] InfoZip distribution contains the file: proginfo/extra.fld
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
class ezcArchiveLocalFileHeader
{
    /**
     * Defines the signature of this header.
     */
    const magic = 0x04034b50;

    /**
     * Extra field definition.
     *
     * UNIX Extra Field ID ("UX").
     */
    const EF_IZUNIX  = 0x5855;

    /**
     * Extra field definition.
     *
     * Info-ZIP's new Unix( "Ux" ).
     */
    const EF_IZUNIX2 = 0x7855;

    /**
     * Extra field definition.
     *
     * Universal timestamp( "UT" ).
     */
    const EF_TIME    = 0x5455;

    /**
     * Minimal UT field contains Flags byte
     */
    const EB_UT_MINLEN = 1;

    /**
     * Byte offset of Flags field
     */
    const EB_UT_FLAGS  = 0;

    /**
     * Byte offset of 1st time value
     */
    const EB_UT_TIME1  = 1;

    /**
     * mtime present
     */
    const EB_UT_FL_MTIME = 1;

    /**
     * atime present
     */
    const EB_UT_FL_ATIME = 2;

    /**
     * ctime present
     */
    const EB_UT_FL_CTIME = 4;

    /**
     * UT field length.
     */
    const EB_UT_FL_LEN = 4;

    /**
     * Minimal "UX" field contains atime, mtime
     */
    const EB_UX_MINLEN = 8;

    /**
     * Offset of atime in "UX" extra field data
     */
    const EB_UX_ATIME  = 0;

    /**
     * Offset of mtime in "UX" extra field data
     */
    const EB_UX_MTIME  = 4;

    /**
     * Full "UX" field ( atime, mtime, uid, gid )
     */
    const EB_UX_FULLSIZE = 12;

    /**
     * Byte offset of UID in "UX" field data
     */
    const EB_UX_UID = 8;

    /**
     * Byte offset of GID in "UX" field data
     */
    const EB_UX_GID = 10;

    /**
     * Minimal Ux field contains UID/GID
     */
    const EB_UX2_MINLEN = 4;

    /**
     * Byte offset of UID in "Ux" field data
     */
    const EB_UX2_UID = 0;

    /**
     * Byte offset of GID in "Ux" field data
     */
    const EB_UX2_GID = 2;

    /**
     * Byte offset of valid in "Ux" field data
     */
    const EB_UX2_VALID =  256;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Holds the user ID and is false if not set.
     *
     * @var int
     */
    protected $userId = false;

    /**
     * Holds the group ID and is false if not set.
     *
     * @var int
     */
    protected $groupId = false;

    /**
     * Holds the modification time as a timestamp and is false if not set.
     *
     * @var int
     */
    protected $mtime = false;

    /**
     * Holds the modification time as a timestamp and is false if not set.
     *
     * @var int
     */
    protected $atime = false;

    /**
     * Holds the creation time as a timestamp and is false if not set.
     *
     * @var int
     */
    protected $ctime = false;

    /**
     * Creates and initializes a new header.
     *
     * If the ezcArchiveCharacterFile $file is null then the header will be empty.
     * When an ezcArchiveCharacterFile is given, the file position should be directly after the
     * signature of the header. This header will be read from the file and initialized in this class.
     *
     * @param ezcArchiveCharacterFile $file
     */
    public function __construct( ezcArchiveCharacterFile $file = null, ezcArchiveCentralDirectoryHeader $centralHeader = null )
    {
        // Offset | Field size |  Description
        // ----------------------------------
        //  0     | 4          | Local file header signature ( 0x04034b50 ).
        //  4     | 2          | Version needed to extract.
        //  6     | 2          | General purpose bit flag.
        //  8     | 2          | Compression method.
        //  10    | 2          | Last mod file time.
        //  12    | 2          | Last mod file date.
        //  14    | 4          | Crc-32.
        //  18    | 4          | Compressed size.
        //  22    | 4          | Uncompressed size.
        //  26    | 2          | File name length.
        //  28    | 2          | Extra field length.

        if ( !is_null( $file ) )
        {
            $this->properties = unpack (
                "vversion/".
                "vbitFlag/".
                "vcompressionMethod/".
                "vlastModFileTime/".
                "vlastModFileDate/".
                "Vcrc/".
                "VcompressedSize/".
                "VuncompressedSize/".
                "vfileNameLength/".
                "vextraFieldLength",
                $file->read( 26 ) );

            $this->properties["fileName"] = $file->read( $this->properties["fileNameLength"] );
            $extraField = $file->read( $this->properties["extraFieldLength"] );

            // Append extra field information.
            $this->setExtraFieldData( $extraField );

            // Fix the local file headers from the central header, in case crc,
            // compressed size and original size are not set. This is needed
            // for Open Office documents at least.
            if ( $centralHeader && $this->properties['compressedSize'] === 0 && $this->properties['uncompressedSize'] === 0 )
            {
                $this->properties['compressedSize'] = $centralHeader->compressedSize;
                $this->properties['uncompressedSize'] = $centralHeader->uncompressedSize;
                $this->properties['crc'] = $centralHeader->crc;
            }

            // Skip the file.
            $file->seek( $this->compressedSize, SEEK_CUR );
        }
    }

    /**
     * Serializes this header and appends it to the given ezcArchiveCharacterFile $archiveFile.
     *
     * @param ezcArchiveCharacterFile $archiveFile
     * @return void
     */
    public function writeEncodedHeader( $archiveFile )
    {
        $this->properties["extraFieldLength" ] = 21; // FIXME.

        $enc = pack( "VvvvvvVVVvv",
            self::magic,
            $this->properties["version"],
            $this->properties["bitFlag"],
            $this->properties["compressionMethod"],
            $this->properties["lastModFileTime"],
            $this->properties["lastModFileDate"],
            $this->properties["crc"],
            $this->properties["compressedSize"],
            $this->properties["uncompressedSize"],
            $this->properties["fileNameLength"],
            $this->properties["extraFieldLength"] );

        $time = pack( "vvcVV", self::EF_TIME, 9, 3, $this->mtime, $this->atime ); // set accesss time to modification time.
        $unix2 = pack( "vvvv", self::EF_IZUNIX2, 4, $this->userId, $this->groupId );

        $archiveFile->write( $enc . $this->fileName . $time . $unix2 );
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist
     * @throws ezcBasePropertyReadOnlyException if the property is read-only
     * @param string $name
     * @param mixed $value
     * @return void
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
             case "version":
             case "bitFlag":
             case "compressionMethod":
             case "crc":
             case "compressedSize":
             case "uncompressedSize":
             case "extraFieldLength":
                $this->properties[$name] = $value;
                break;

             case "lastModFileTime":
             case "lastModFileDate":
             case "fileNameLength":
                throw new ezcBasePropertyReadOnlyException( $name );

             default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case "version":
            case "bitFlag":
            case "compressionMethod":
            case "lastModFileTime":
            case "lastModFileDate":
            case "crc":
            case "compressedSize":
            case "uncompressedSize":
            case "extraFieldLength":
            case "fileNameLength":
            case "fileName":
                return $this->properties[ $name ];

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the modification timestamp.
     *
     * The timestamp from the extra fields is returned if it is available.
     * If it cannot be found, the ZIP time is converted to a timestamp.
     *
     * @return int
     */
    public function getModificationTime()
    {
        return ( $this->mtime ? $this->mtime : self::dosFormatToTimestamp( array( $this->lastModFileTime, $this->lastModFileDate ) ) );
    }

    /**
     * Sets the modification time to the int $timestamp.
     *
     * The properties lastModFileTime and lastModFileDate properties will be set.
     *
     * @param int $timestamp
     * @return void
     */
    public function setModificationTime( $timestamp )
    {
        $this->mtime = $timestamp;
        list( $this->properties["lastModFileTime"], $this->properties["lastModFileDate"] ) = self::timestampToDosFormat( $timestamp );
    }

    /**
     * Returns an two element array with, respectively, the dos time and dos date, which are converted from
     * the given int $timestamp.
     *
     * The DOS time and date format are described here: {@link http://www.vsft.com/hal/dostime.htm}.
     *
     * @see http://www.vsft.com/hal/dostime.htm
     *
     * @param int $timestamp
     * @return array(int)
     */
    public static function timestampToDosFormat( $timestamp )
    {
        $time = getdate( $timestamp );

        $dosTime = ( $time["hours"]  & 31 ); // Hours. Bit: 11 .. 15
        $dosTime <<= 6;
        $dosTime |= ( $time["minutes"] & 63 ); // Minutes.
        $dosTime <<= 5;
        $dosTime |= ( ( int ) ( $time["seconds"] / 2 ) & 31 );  // seconds.

        $dosDate = ( $time["year"] - 1980 ) & 127;  // Year.
        $dosDate <<= 4;
        $dosDate |= ( $time["mon"] & 15 ); // month.
        $dosDate <<= 5;
        $dosDate |= ( $time["mday"] & 31 ); // day.

        return array( $dosTime, $dosDate );
    }

    /**
     * Returns the timestamp which is converted from an array with, respectively, the dos time and dos date.
     *
     * Expects an array with two elements.
     *
     * The DOS time and date format are described here: {@link http://www.vsft.com/hal/dostime.htm}.
     *
     * @see http://www.vsft.com/hal/dostime.htm
     *
     * @param array(int) $array
     * @return int
     */
    public static function dosFormatToTimestamp( array $array )
    {
        $dosTime = $array[0];

        $seconds = ( ($dosTime ) & 31 ) * 2 ;  // Bit  0 .. 4
        $minutes = ( $dosTime >>  5 ) & 63 ;          // Bit  5 .. 10
        $hours = ( $dosTime >> 11 ) & 31;               // Bit 11 .. 15

        $dosDate = $array[1];
        $day =  ( ( $dosDate ) & 31 );          // Bit  0 .. 4
        $month = ( $dosDate >>  5 ) & 15 ;            // Bit  5 .. 8
        $year = ( ( $dosDate >> 9 ) & 127 ) + 1980;     // Bit  9 .. 15

        return mktime( $hours, $minutes, $seconds, $month, $day, $year );
    }

    /**
     * Sets the filename in the header.
     *
     * The properties fileName and file length are changed.
     *
     * @param string $name
     */
    public function setFileName( $name )
    {
        $this->properties["fileName"] = $name;
        $this->properties["fileNameLength"] = strlen( $name );
    }

    /**
     * Sets the compression.
     *
     * @param int $compressionMethod
     * @param int $compressedSize
     * @return void
     */
    public function setCompression( $compressionMethod, $compressedSize )
    {
        // Fixme, for now only decompressed.
        $this->compressionMethod = $compressionMethod;
        $this->compressedSize = $compressedSize;

    }

    /**
     * Updates the given ezcArchiveFileStructure $struct with the values from this header.
     *
     * If bool $override is false, this method will not overwrite the values from the ezcArchiveFileStructure $struct.
     * The values that can be set in the archiveFileStructure are: path, gid, uid, mtime, and size.
     *
     * @param ezcArchiveFileStructure &$struct
     * @param bool $override
     * @return void
     */
    public function setArchiveFileStructure( ezcArchiveFileStructure &$struct, $override = false )
    {
        if ( !isset( $struct->path ) || $override )
        {
            $struct->path = $this->fileName;
        }

        if ( !isset( $struct->size ) || $override )
        {
            $struct->size = $this->uncompressedSize;
        }

        if ( $this->groupId && ( !isset( $struct->size )  || $override ) )
        {
            $struct->gid   = $this->groupId;
        }

        if ( $this->userId &&  ( !isset( $struct->size )  || $override ) )
        {
            $struct->uid   = $this->userId;
        }

        if ( $this->mtime &&   ( !isset( $struct->mtime ) || $override ) )
        {
            $struct->mtime = $this->mtime;
        }

        if ( $this->atime &&   ( !isset( $struct->atime ) || $override ) )
        {
            $struct->atime = $this->atime;
        }
    }

    /**
     * Sets this header with the values from the ezcArchiveEntry $entry.
     *
     * The values that are possible to set from the ezcArchiveEntry $entry are set in this header.
     *
     * @param ezcArchiveEntry $entry
     * @return void
     */
    public function setHeaderFromArchiveEntry( ezcArchiveEntry $entry )
    {
        $this->version = 10; // FIXME
        $this->bitFlag = 0; // FIXME

        $this->atime = $entry->getAccessTime();

        $this->groupId = $entry->getGroupId();
        $this->userId = $entry->getUserId();

        $this->setModificationTime( $entry->getModificationTime() );

        if ( $entry->isSymLink() )
        {
            $this->uncompressedSize = strlen( $entry->getLink() );
            $this->crc = ezcArchiveChecksums::getCrc32FromString( $entry->getLink() );
        }
        else
        {
            $this->uncompressedSize = $entry->getSize();
            $this->crc = ezcArchiveChecksums::getCrc32FromFile( $entry->getPath() );
        }

        $this->setFileName( $entry->getPath( false ) );
    }

    /**
     * Returns the total size of this header.
     *
     * @return int
     */
    public function getHeaderSize()
    {
        return 30 + $this->properties["fileNameLength"] + $this->properties["extraFieldLength"];
    }

    /**
     * Decodes and sets the extra header properties from the string $data.
     *
     * @param string $data
     * @return void
     */
    protected function setExtraFieldData( $data )
    {
        $raw = array();

        $offset = 0;
        $dataLength = strlen( $data );

        while ( $offset < $dataLength )
        {
            // Read header.
            $dec = unpack( "vid/vlength", substr( $data, $offset, 4 ) );
            $offset += 4;

            switch ( $dec["id"] )
            {
                case self::EF_IZUNIX2:
                    $raw[ "EF_IZUNIX2" ] = $this->getNewInfoZipExtraField( substr( $data, $offset, $dec["length"] ), $dec["length"] );
                    break;

                case self::EF_IZUNIX:
                    $raw[ "EF_IZUNIX" ] = $this->getOldInfoZipExtraField( substr( $data, $offset, $dec["length"] ), $dec["length"] );
                    break;

                case self::EF_TIME:
                    $raw[ "EF_TIME" ] = $this->getUniversalTimestampField( substr( $data, $offset, $dec["length"] ), $dec["length"] );
                    break;
            }

            $offset += $dec["length"];
        }

        $result = array();

        // The order is important.
        if ( isset( $raw["EF_TIME"]["mtime"] ) )
        {
            $this->mtime = $raw["EF_TIME"]["mtime"];
        }

        if ( isset( $raw["EF_TIME"]["atime"] ) )
        {
            $this->atime = $raw["EF_TIME"]["atime"];
        }

        if ( isset( $raw["EF_TIME"]["ctime"] ) )
        {
            $this->ctime = $raw["EF_TIME"]["ctime"];
        }

        if ( isset( $raw["EF_IZUNIX"]["mtime"] ) )
        {
            $this->mtime = $raw["EF_IZUNIX"]["mtime"];
        }

        if ( isset( $raw["EF_IZUNIX"]["atime"] ) )
        {
            $this->atime = $raw["EF_IZUNIX"]["atime"];
        }

        if ( isset( $raw["EF_IZUNIX"]["gid"] ) )
        {
            $this->groupId = $raw["EF_IZUNIX"]["gid"];
        }

        if ( isset( $raw["EF_IZUNIX"]["uid"] ) )
        {
            $this->userId = $raw["EF_IZUNIX"]["uid"];
        }

        if ( isset( $raw["EF_IZUNIX2"]["gid"] ) )
        {
            $this->groupId = $raw["EF_IZUNIX2"]["gid"];
        }

        if ( isset( $raw["EF_IZUNIX2"]["uid"] ) )
        {
            $this->userId = $raw["EF_IZUNIX2"]["uid"];
        }

        return $result;
    }

    /**
     * Returns an array with the decoded 'new Info-Zip' data.
     *
     * @param string $field
     * @param int $length
     * @return array(string)
     */
    protected function getNewInfoZipExtraField( $field, $length )
    {
        $localOffset = 0;

        if ( $length >= self::EB_UX2_MINLEN )
        {
            return unpack( "vuid/vgid", substr( $field, 0, self::EB_UX2_MINLEN ) );
        }

        return array();
    }

    /**
     * Returns an array with the decoded 'old Info-Zip' data.
     *
     * @param string $field
     * @param int $length
     * @return array(string)
     */
    protected function getOldInfoZipExtraField( $field, $length )
    {
        if ( $length >= self::EB_UX_FULLSIZE )
        {
            return unpack( "Vatime/Vmtime/vuid/vgid", substr( $field, 0, self::EB_UX_FULLSIZE ) );
        }
        else if ( $length >= self::EB_UX_MINLEN )
        {
            return unpack( "Vatime/Vmtime", substr( $field, 0, self::EB_UX_MINLEN ) );
        }

        return array();
    }

    /**
     * Returns an array with the decoded 'universal timestamp' data.
     *
     * @param string $field
     * @param int $length
     * @return array(string)
     */
    protected function getUniversalTimestampField( $field, $length )
    {
        $localOffset = 0;
        $result = array();

        if ( $length >= self::EB_UT_MINLEN )
        {
            $f = unpack( "cflags", substr( $field, $localOffset, 1 ) );
            $length--; // Flag takes one byte.

            if ( ( $f["flags"] & self::EB_UT_FL_MTIME ) && $length >= 4 )
            {
                $result = array_merge( $result, unpack( "Vmtime", substr( $field, $localOffset + self::EB_UT_MINLEN + ( self::EB_UT_FL_LEN * 0 ), self::EB_UT_FL_LEN ) ) );
                $length -= 4;
            }

            if ( $f["flags"] & self::EB_UT_FL_ATIME  && $length >= 4 )
            {
                $result = array_merge( $result, unpack( "Vatime", substr( $field, $localOffset + self::EB_UT_MINLEN + ( self::EB_UT_FL_LEN * 1 ), self::EB_UT_FL_LEN ) ) );
                $length -= 4;
            }

            if ( $f["flags"] & self::EB_UT_FL_CTIME && $length >= 4 )
            {
                $result = array_merge( $result, unpack( "Vctime", substr( $field, $localOffset + self::EB_UT_MINLEN + ( self::EB_UT_FL_LEN * 2 ), self::EB_UT_FL_LEN ) ) );
                $length -= 4;
            }
        }

        return $result;
    }

    /**
     * Returns true if the given string $string matches with the current signature.
     *
     * @param string $string
     * @return bool
     */
    public static function isSignature( $string )
    {
        return $string == pack( "V", self::magic );
    }
}
?>
