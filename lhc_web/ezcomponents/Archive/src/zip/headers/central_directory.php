<?php
/**
 * File containing the ezcArchiveCentralDirectoryHeader class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcArchiveCentralDirectoryHeader class represents the Zip central directory header.
 *
 * ezcArchiveCentralDirectoryHeader can read the header from an ezcArchiveCharacterFile or ezcArchiveEntry.
 *
 * The values from the headers are directly accessible via the class properties, and allows
 * reading and writing to specific header values.
 *
 * The entire header can be appended to an ezcArchiveCharacterFile again or written to an ezcArchiveFileStructure.
 * Information may get lost, though.
 *
 * The central directory format[1]:
 *
 * <pre>
 *  [file header 1]
 *  .
 *  .
 *  .
 *  [file header n]
 *  [digital signature] <-- Optional, TODO: check if implemented.
 * </pre>
 *
 * The Central Directory Header has the following structure:
 *
 * <pre>
 * + ---+---------+------------+------------------------+------------------------------------+
 * | ID | Offset  | Field size | Property               |  Description                       |
 * +----+---------+------------+------------------------+------------------------------------+
 * |    |  0      | 4          | -                      | Central directory header signature |
 * |    |  4      | 2          | versionMadeBy          | Version made by                    |
 * |    |  6      | 2          | versionNeededToExtract | Version needed to extract          |
 * |    |  8      | 2          | bitFlag                | General purpose bit flag           |
 * |    |  10     | 2          | compressionMethod      | Compression method                 |
 * |    |  12     | 2          | lastModFileTime        | Last modification file time        |
 * |    |  14     | 2          | lastModFileDate        | Last modification file date        |
 * |    |  16     | 4          | crc                    | crc-32                             |
 * |    |  20     | 4          | compressedSize         | compressed size                    |
 * |    |  24     | 4          | uncompressedSize       | uncompressed size                  |
 * | X: |  26     | 2          | fileNameLength         | file name length                   |
 * | Y: |  28     | 2          | extraFieldLength       | extra field length                 |
 * | Z: |  30     | 2          | fileCommentLength      | file comment length                |
 * |    |  32     | 2          | diskNumberStart        | disk number start                  |
 * |    |  34     | 2          | internalFileAttributes | internal file attributes           |
 * |    |  38     | 4          | externalFileAttributes | external file attributes           |
 * |    |  42     | 4          | relativeHeaderOffset   | relative offset of local header    |
 * |    |  46     | X          | fileName               | file name                          |
 * |    |  46+X   | Y          | -                      | extra field                        |
 * |    |  46+X+Y | Z          | comment                | file comment                       |
 * +----+---------+------------+------------------------+------------------------------------+
 * </pre>
 *
 * The columns of the table are:
 * - ID gives a label to a specific field or row in the table.
 * - Offset describes the start position of a header field.
 * - Field size describes the size of the field in bytes.
 * - Property is the name of the property that will be set by the header field.
 * - Description explains what this field describes.
 *
 * The central directory signature cannot be changed and is set in the constant {@link self::magic}.
 *
 * The extra fields that are implemented and references to extra documentation can be found in
 * the {@link ezcArchivelocalFileHeader}.
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
class ezcArchiveCentralDirectoryHeader extends ezcArchiveLocalFileHeader
{
    /**
     * Defines the signature of this header.
     */
    const magic = 0x02014b50;

    /**
     * Creates and initializes a new header.
     *
     * If the ezcArchiveCharacterFile $file is null then the header will be empty.
     * When an ezcArchiveCharacterFile is given, the file position should be directly after the
     * signature of the header. This header will be read from the file and initialized in this class.
     *
     * @param ezcArchiveCharacterFile $file
     */
    public function __construct( ezcArchiveCharacterFile $file = null )
    {
        if ( !is_null( $file ) )
        {
            $this->properties = unpack (
                "vversionMadeBy/".
                "vversionNeededToExtract/".
                "vbitFlag/".
                "vcompressionMethod/".
                "vlastModFileTime/".
                "vlastModFileDate/".
                "Vcrc/".
                "VcompressedSize/".
                "VuncompressedSize/".
                "vfileNameLength/".
                "vextraFieldLength/".
                "vfileCommentLength/".
                "vdiskNumberStart/".
                "vinternalFileAttributes/".
                "VexternalFileAttributes/".
                "VrelativeHeaderOffset",
                $file->read( 42 ) );

                $this->properties["fileName"] = $file->read( $this->properties["fileNameLength"] );
                $extraField = $file->read( $this->properties["extraFieldLength"] ); // FIXME, extra fields.
                $this->properties["comment"] = $file->read( $this->properties["fileCommentLength"] );

                // Append extra field information.
                $this->setExtraFieldData( $extraField );
        }
        else
        {
            // Some default values:
            $this->properties["versionMadeBy"] = 791;
            $this->properties["versionNeededToExtract"] = 10;
            $this->properties["diskNumberStart"] = 0;
            $this->setComment( "" );
        }
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
            case "versionMadeBy":
            case "versionNeededToExtract":
            case "bitFlag":
            case "compressionMethod":
            case "lastModFileTime":
            case "lastModFileDate":
            case "crc":
            case "compressedSize":
            case "uncompressedSize":
            case "diskNumberStart":
            case "internalFileAttributes":
            case "externalFileAttributes":
            case "fileName":
            case "relativeHeaderOffset":
                $this->properties[$name] = $value;
                break;

            case "comment":
                $this->setComment( $value );
                break;

            case "fileNameLength":
            case "extraFieldLength":
            case "fileCommentLength":
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
            case "versionMadeBy":
            case "versionNeededToExtract":
            case "bitFlag":
            case "compressionMethod":
            case "lastModFileTime":
            case "lastModFileDate":
            case "crc":
            case "compressedSize":
            case "uncompressedSize":
            case "diskNumberStart":
            case "internalFileAttributes":
            case "externalFileAttributes":
            case "relativeHeaderOffset":
            case "fileNameLength":
            case "extraFieldLength":
            case "fileCommentLength":
            case "fileName":
            case "comment":
                return $this->properties[$name];

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Sets the comment to string $comment and updates the comment length.
     *
     * @param string $comment
     * @return void
     */
    public function setComment( $comment )
    {
        $this->properties["comment"] = $comment;
        $this->properties["fileCommentLength"] = strlen( $comment );
    }

    /**
     * Sets the type to int $type.
     *
     * The type is a constant from the {@link ezcArchiveEntry}. For example:
     * ezcArchiveEntry::IS_FIFO.
     * The property externalFileAttributes will be changed to reflect the $type.
     *
     * @throws ezcArchiveException
     *         if $type is unknown
     * @param int $type
     * @return void
     */
    public function setType( $type )
    {
        $ext = 0;
        switch ( $type )
        {
            case ezcArchiveEntry::IS_FIFO:
                $ext = ezcArchiveStatMode::S_IFIFO;
                break;

            case ezcArchiveEntry::IS_CHARACTER_DEVICE:
                $ext = ezcArchiveStatMode::S_IFCHR;
                break;

            case ezcArchiveEntry::IS_DIRECTORY:
                $ext = ezcArchiveStatMode::S_IFDIR;
                break;

            case ezcArchiveEntry::IS_BLOCK_DEVICE:
                $ext = ezcArchiveStatMode::S_IFBLK;
                break;

            case ezcArchiveEntry::IS_FILE:
                $ext = ezcArchiveStatMode::S_IFREG;
                break;

            case ezcArchiveEntry::IS_SYMBOLIC_LINK:
                $ext = ezcArchiveStatMode::S_IFLNK;
                break;

            default:
                throw new ezcArchiveException( "Unknown type" );
        }

        if ( !isset( $this->properties["externalFileAttributes"] ) )
        {
            $this->properties["externalFileAttributes"] = 0;
        }

        $ext <<= 16;
        $clear = ( $this->properties["externalFileAttributes"] & ezcArchiveStatMode::S_IFMT << 16 );

        $this->properties["externalFileAttributes"] ^= $clear; // Remove the bits from S_IFMT, because we XOR with self.
        $this->properties["externalFileAttributes"] |= $ext; // And add the ext bits.
    }

    /**
     * Returns the type of the file.
     *
     * The type is read and translated from the externalFileAttributes property.
     *
     * @return int  An ezcArchiveEntry constant.
     */
    public function getType()
    {
        $extAttrib = ( $this->properties["externalFileAttributes"] >> 16 );

        switch ( $extAttrib & ezcArchiveStatMode::S_IFMT )
        {
            case ezcArchiveStatMode::S_IFIFO:
                return ezcArchiveEntry::IS_FIFO;

            case ezcArchiveStatMode::S_IFCHR:
                return ezcArchiveEntry::IS_CHARACTER_DEVICE;

            case ezcArchiveStatMode::S_IFDIR:
                return ezcArchiveEntry::IS_DIRECTORY;

            case ezcArchiveStatMode::S_IFBLK:
                return ezcArchiveEntry::IS_BLOCK_DEVICE;

            case ezcArchiveStatMode::S_IFREG:
                return ezcArchiveEntry::IS_FILE;

            case ezcArchiveStatMode::S_IFLNK:
                return ezcArchiveEntry::IS_SYMBOLIC_LINK;

            default:
                if ( substr( $this->properties["fileName"], -1 ) == "/" )
                {
                    return ezcArchiveEntry::IS_DIRECTORY;
                }
                else
                {
                    return ezcArchiveEntry::IS_FILE;
                }
        }
    }

    /**
     * Returns the permissions (as a decimal number) of the file.
     *
     * The type is read and translated from the externalFileAttributes property.
     *
     * @return int
     */
    public function getPermissions()
    {
        $extAttrib = ( $this->properties["externalFileAttributes"] >> 16 );

        return decoct( $extAttrib & ezcArchiveStatMode::S_PERM_MASK );
    }

    /**
     * Sets the permissions (as a decimal number) of the file to int $permissions.
     *
     * The $permissions expects an decimal number.
     *
     * The externalFileAttributes property will be changed.
     *
     * @param int $permissions
     */
    public function setPermissions( $permissions )
    {
        $perm = octdec( $permissions );
        $perm <<= 16;

        if ( !isset( $this->properties["externalFileAttributes"] ) )
        {
            $this->properties["externalFileAttributes"] = 0;
        }

        $clear = ( $this->properties["externalFileAttributes"] & ezcArchiveStatMode::S_PERM_MASK << 16 );
        $this->properties["externalFileAttributes"] ^= $clear; // Remove the bits from S_PERM_MASK, because we XOR with self.
        $this->properties["externalFileAttributes"] |= $perm; // And add the perm bits.
    }

    /**
     * Updates the given ezcArchiveFileStructure $struct with the values from this header.
     *
     * If bool $override is false, this method will not overwrite the values from the ezcArchiveFileStructure $struct.
     * The values that can be set in the archiveFileStructure are: path, mode, type, size.
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

        if ( !isset( $struct->mode ) || $override )
        {
            $struct->mode = ( $this->getPermissions() == 0 ? false : $this->getPermissions() );
        }

        if ( !isset( $struct->type ) || $override )
        {
            $struct->type = $this->getType();
        }

        if ( !isset( $struct->size ) || $override )
        {
            $struct->size = $this->uncompressedSize;
        }
    }

    /**
     * Returns the total size of this header.
     *
     * @return int
     */
    public function getHeaderSize()
    {
        return 46 + $this->properties["fileNameLength"] + $this->properties["extraFieldLength"] + $this->properties["fileCommentLength"];
    }

    /**
     * Sets this header with the values from the {@link ezcArchiveLocalFileHeader}.
     *
     * The properties that are set: versionNeededToExtract, bitFlag, compressionMethod,
     * lastModFileTime, lastModFileDate, crc, compressedSize, uncompressedSize, fileNameLength,
     * and fileName.
     *
     * @param ezcArchiveLocalFileHeader $localFileHeader
     * @return void
     */
    public function setHeaderFromLocalFileHeader( ezcArchiveLocalFileHeader $localFileHeader )
    {
        $this->properties["versionNeededToExtract"] = $localFileHeader->version;
        $this->properties["bitFlag"] = $localFileHeader->bitFlag;
        $this->properties["compressionMethod"] = $localFileHeader->compressionMethod;
        $this->properties["lastModFileTime"] = $localFileHeader->lastModFileTime;
        $this->properties["lastModFileDate"] = $localFileHeader->lastModFileDate;
        $this->properties["crc"] = $localFileHeader->crc;
        $this->properties["compressedSize"] = $localFileHeader->compressedSize;
        $this->properties["uncompressedSize"] = $localFileHeader->uncompressedSize;
        $this->properties["fileNameLength"] = $localFileHeader->fileNameLength;
        $this->properties["fileName"] = $localFileHeader->fileName;
    }

    /**
     * Sets this header with the values from the ezcArchiveEntry $entry.
     *
     * The values that are possible to set from the ezcArchiveEntry $entry are set in this header.
     * The properties that change are: internalFileAttributes, relativeHeaderOffset, type, and mtime.
     *
     * @param ezcArchiveEntry $entry
     * @return void
     */
    public function setHeaderFromArchiveEntry( ezcArchiveEntry $entry )
    {
        $this->properties["internalFileAttributes"] = 0;

        $this->setType( $entry->getType() );
        $this->setPermissions( $entry->getPermissions() );

        $this->properties["relativeHeaderOffset"] = 0;
        $this->properties["mtime"] = $entry->getModificationTime();
    }

    /**
     * Serializes this header and appends it to the given ezcArchiveCharacterFile $archiveFile.
     *
     * @param ezcArchiveCharacterFile $archiveFile
     * @return void
     */
    public function writeEncodedHeader( $archiveFile )
    {
        $this->properties["extraFieldLength" ] = 13; // 9 + 4.

        $enc = pack( "VvvvvvvVVVvvvvvVV",
            self::magic,           // V magic number
            $this->versionMadeBy,  // v
            $this->versionNeededToExtract,  // v
            $this->bitFlag, // v
            $this->compressionMethod, // v
            $this->lastModFileTime, // v
            $this->lastModFileDate, // v
            $this->crc, // V
            $this->compressedSize, // V
            $this->uncompressedSize, // V
            $this->fileNameLength, // v
            $this->extraFieldLength, // v extra data.
            $this->fileCommentLength , // v Comment
            $this->diskNumberStart ,  // v disknumber start
            $this->internalFileAttributes,  // v Internal attribute
            $this->externalFileAttributes,  // V external attributes
            $this->relativeHeaderOffset       // V relative header offset?
         );

         $time = pack( "vvcV", self::EF_TIME, 5, 1, $this->mtime ); // fixme atime?
         $unix2 = pack( "vv", self::EF_IZUNIX2, 0 ); // Add empty unix2 stamp.

         $archiveFile->write( $enc . $this->fileName . $time . $unix2 . $this->comment );
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
