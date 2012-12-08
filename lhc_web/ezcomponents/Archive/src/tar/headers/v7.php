<?php
/**
 * File contains the ezcArchiveV7Header class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcArchiveV7Header class represents the Tar V7 header.
 *
 * ezcArchiveV7Header can read the header from an ezcArchiveBlockFile or ezcArchiveEntry.
 *
 * The values from the headers are directly accessible via the class properties, and allows
 * reading and writing to specific header values.
 *
 * The entire header can be appended to an ezcArchiveBlockFile again or written to an ezcArchiveFileStructure.
 * Information may get lost, though.
 *
 * The V7 Header has the following structure:
 *
 * <pre>
 * +--------+------------+------------------+-----------------------------------------------------------+
 * | Offset | Field size | Property         |  Description                                              |
 * +--------+------------+------------------+-----------------------------------------------------------+
 * |  0     | 100        | fileName         | Name of file                                              |
 * |  100   | 8          | fileMode         | File mode                                                 |
 * |  108   | 8          | userId           | Owner user ID                                             |
 * |  116   | 8          | groupId          | Owner group ID                                            |
 * |  124   | 12         | fileSize         | Length of file in bytes                                   |
 * |  136   | 12         | modificationTime | Modify time of file                                       |
 * |  148   | 8          | checksum         | Checksum for header                                       |
 * |  156   | 1          | type             | Indicator for links                                       |
 * |  157   | 100        | linkName         | Name of linked file                                       |
 * |  257   | 72         | -                | NUL.                                                      |
 * |  329   | 8          | -                | Compatibility with GNU tar: Major device set to: 00000000.|
 * |  337   | 8          | -                | Compatibility with GNU tar: Minor device set to: 00000000.|
 * |  345   | 167        | -                | NUL.                                                      |
 * +--------+------------+------------------+-----------------------------------------------------------+
 * </pre>
 *
 * The columns of the table are:
 * - Offset describes the start position of a header field.
 * - Field size describes the size of the field.
 * - Property is the name of the property that will be set by the header field.
 * - Description explains what this field describes.
 *
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
class ezcArchiveV7Header
{
    /**
     * Relative byte position that the header starts.
     */
    const START_HEADER    = 0;

    /**
     * Relative byte position that the checksum starts.
     */
    const CHECKSUM_OFFSET = 148;

    /**
     * Number of bytes that the checksum occupies.
     */
    const CHECKSUM_SIZE   = 8;

    /**
     * Relative byte position that the checksum ends.
     */
    const END_HEADER      = 512;

    /**
     * Number of bytes that a block occupies.
     */
    const BLOCK_SIZE      = 512;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array();

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @return void
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case "fileName":
            case "fileMode":
            case "userId":
            case "groupId":
            case "fileSize":
            case "modificationTime":
            case "checksum":
            case "type":
            case "linkName":
                $this->properties[$name] = $value;
                break;

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
            case "fileName":
            case "fileMode":
            case "userId":
            case "groupId":
            case "fileSize":
            case "modificationTime":
            case "checksum":
            case "type":
            case "linkName":
                return $this->properties[ $name ];

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Creates and initializes a new header.
     *
     * If the ezcArchiveBlockFile $file is null then the header will be empty.
     * When an ezcArchiveBlockFile is given, the block position should point to the header block.
     * This header block will be read from the file and initialized in this class.
     *
     * @throws ezcArchiveChecksumException
     *         if the checksum from the file did not match the calculated one
     * @param ezcArchiveBlockFile $file
     */
    public function __construct( ezcArchiveBlockFile $file = null )
    {
        // Offset | Field size |  Description
        // ----------------------------------
        //  0     | 100        | Name of file
        //  100   | 8          | File mode
        //  108   | 8          | Owner user ID
        //  116   | 8          | Owner group ID
        //  124   | 12         | Length of file in bytes
        //  136   | 12         | Modify time of file
        //  148   | 8          | Checksum for header
        //  156   | 1          | Indicator for links
        //  157   | 100        | Name of linked file
        // ------------------------------------------
        //  257   | 72         | NUL.
        //  329   | 8          | Compatibility with GNU tar: Major device set to: 00000000.
        //  337   | 8          | Compatibility with GNU tar: Minor device set to: 00000000.
        //  345   | 167        | NUL.

        if ( !is_null( $file ) )
        {
            $this->properties = unpack ( "a100fileName/".
                                         "a8fileMode/".
                                         "a8userId/".
                                         "a8groupId/".
                                         "a12fileSize/".
                                         "a12modificationTime/".
                                         "a8checksum/".
                                         "a1type/".
                                         "a100linkName", $file->current() );

            $this->properties["userId"]   = octdec( $this->properties["userId"] );
            $this->properties["groupId"]  = octdec( $this->properties["groupId"] );
            $this->properties["fileSize"]  = octdec( $this->properties["fileSize"] );
            $this->properties["modificationTime"]  = octdec( $this->properties["modificationTime"] );
            $this->properties["checksum"]  = octdec( $this->properties["checksum"] );

            if ( !$this->checksum( $this->checksum, $file->current() ) )
            {
                throw new ezcArchiveChecksumException( $file->getFileName() );
            }
        }
    }

    /**
     * Returns true when the checksum $checksum matches with the header data $rawHeader; otherwise returns false.
     *
     * The checksum is calculated by the {@link ezcArchiveChecksums::getTotalByteValueFromString()}.
     *
     * @param  int    $checksum
     * @param  string $rawHeader
     * @return bool
     */
    protected function checksum( $checksum, $rawHeader )
    {
        $total = ezcArchiveChecksums::getTotalByteValueFromString( substr( $rawHeader, self::START_HEADER, self::END_HEADER - self::START_HEADER ) );

        // assume blanks for the checksum itself.
        $total -= ezcArchiveChecksums::getTotalByteValueFromString( substr( $rawHeader, self::CHECKSUM_OFFSET, self::CHECKSUM_SIZE ) );
        $total += ezcArchiveChecksums::getTotalByteValueFromString( str_repeat( " ", self::CHECKSUM_SIZE ) );

        return ( strcmp( sprintf( "%08x", $checksum ), sprintf( "%08x", $total ) ) == 0 );
    }

    /**
     * Returns the encoded header as given as the parameter $encodedHeader but includes the
     * checksum of the header.
     *
     * The encoded header $encodedHeader should have spaces at the place where the checksum should be stored.
     *
     * @param string $encodedHeader
     * @return string
     */
    protected function setChecksum( $encodedHeader )
    {
        $total = ezcArchiveChecksums::getTotalByteValueFromString( $encodedHeader );

        $checksum = pack( "a7", str_pad( decoct( $total ), 6, "0", STR_PAD_LEFT ) );
        $checksum .= " ";

        $begin = substr( $encodedHeader, 0, 148 );
        $end = substr( $encodedHeader, 156 );

        return $begin.$checksum.$end;
    }

    /**
     * Sets this header with the values from the ezcArchiveEntry $entry.
     *
     * The values that are possible to set from the ezcArchiveEntry $entry are set in this header.
     * The properties that may change are: fileName, fileMode, userId, groupId, fileSize, modificationTime,
     * linkName, and type.
     *
     * @param ezcArchiveEntry $entry
     * @return void
     */
    public function setHeaderFromArchiveEntry( ezcArchiveEntry $entry )
    {
        $this->fileName = $entry->getPath( false );
        $this->fileMode = $entry->getPermissions();
        $this->userId = $entry->getUserId();
        $this->groupId = $entry->getGroupId();
        $this->fileSize = $entry->getSize();
        $this->modificationTime = $entry->getModificationTime();
        $this->linkName = $entry->getLink( false );

        switch ( $entry->getType() )
        {
            case ezcArchiveEntry::IS_FILE:
                $this->type = "";
                break; // ends up as a \0 character.

            case ezcArchiveEntry::IS_LINK:
                $this->type = 1;
                break;

            case ezcArchiveEntry::IS_SYMBOLIC_LINK:
                $this->type = 2;
                break;

            case ezcArchiveEntry::IS_DIRECTORY:
                $this->type = 5;
                break;

            // Devices, etc are set to \0.
            default:
                $this->type = "";
                break; // ends up as a \0 character.
        }

        $length = strlen( $this->fileName );

        if ( $entry->getType() == ezcArchiveEntry::IS_DIRECTORY )
        {
           // Make sure that the filename ends with a slash.
           if ( $this->fileName[ $length - 1] != "/" )
           {
               $this->fileName .= "/";
           }
        }
        else
        {
           if ( $this->fileName[ $length - 1] == "/" )
           {
               $this->fileName = substr( $header->fileName, 0, -1 ); // Remove last character.
           }
        }
    }

    /**
     * Serializes this header and appends it to the given ezcArchiveBlockFile $archiveFile.
     *
     * @param ezcArchiveBlockFile $archiveFile
     * @return void
     */
    public function writeEncodedHeader( ezcArchiveBlockFile $archiveFile )
    {
        // Offset | Field size |  Description
        // ----------------------------------
        //  0     | 100        | Name of file
        //  100   | 8          | File mode
        //  108   | 8          | Owner user ID
        //  116   | 8          | Owner group ID
        //  124   | 12         | Length of file in bytes
        //  136   | 12         | Modify time of file
        //  148   | 8          | Checksum for header
        //  156   | 1          | Indicator for links
        //  157   | 100        | Name of linked file
        // ------------------------------------------
        //  257   | 72         | NUL.
        //  329   | 8          | Compatibility with GNU tar: Major device set to: 00000000.
        //  337   | 8          | Compatibility with GNU tar: Minor device set to: 00000000.
        //  345   | 167        | NUL.
        //

        $enc = pack( "a100a8a8a8a12a12a8a1a100a72a8a8a167",
            $this->fileName,
            str_pad( $this->fileMode, 7, "0", STR_PAD_LEFT ),
            str_pad( decoct( $this->userId ), 7, "0", STR_PAD_LEFT ),
            str_pad( decoct( $this->groupId ), 7, "0", STR_PAD_LEFT ),
            str_pad( decoct( $this->fileSize ), 11, "0", STR_PAD_LEFT ),
            str_pad( decoct( $this->modificationTime ), 11, "0", STR_PAD_LEFT ),
            "        ",
            $this->type,
            $this->linkName,
            "",
            "0000000",
            "0000000",
            "" );

        $enc = $this->setChecksum( $enc );
        $archiveFile->append( $enc );
    }

    /**
     * Updates the given ezcArchiveFileStructure $struct with the values from
     * this header.
     *
     * The values that can be set in the archiveFileStructure are: path, gid,
     * uid, type, link, mtime, mode, and size.
     *
     * @param ezcArchiveFileStructure &$struct
     * @return void
     */
    public function setArchiveFileStructure( ezcArchiveFileStructure &$struct )
    {
        $struct->path = $this->fileName;
        $struct->gid = $this->groupId;
        $struct->uid = $this->userId;

        switch ( $this->type )
        {
            case "\0":
            case 0:
                $struct->type = ezcArchiveEntry::IS_FILE;
                break;

            case 1:
                $struct->type = ezcArchiveEntry::IS_LINK;
                break;

            case 2:
                $struct->type = ezcArchiveEntry::IS_SYMBOLIC_LINK;
                break;
        }

        // trailing slash means directory
        if ( $this->fileName[ strlen( $this->fileName ) - 1 ] == '/' )
        {
            $struct->type = ezcArchiveEntry::IS_DIRECTORY;
        }

        $struct->link = $this->linkName;
        $struct->mtime = $this->modificationTime;
        $struct->mode = $this->fileMode;
        $struct->size = $this->fileSize;
    }
}
?>
