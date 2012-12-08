<?php
/**
 * File contains the ezcArchiveEntry class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcArchiveEntry class provides system-independent file information.
 *
 * ezcArchiveEntry provides file information about the file path, it's access rights and whether the file is an
 * directory, symbolic link, hard link, block-file, etc. The owner name, the group name, the last access time
 * are also available. ezcArchiveEntry can be used to get the file information directly from the file-system or
 * from an archive.
 *
 * The main purpose of ezcArchiveEntry is to provide information about:
 * - Files on the file-system that should be appended to the archive.
 * - Files currently in the archive that can be extracted to the file-system.
 *
 * Use the {@link getEntryFromFile()} to create an ezcArchiveEntry from a file in the filesystem.
 * Important is that the prefix is set.
 * This specifies which part of the path should be stripped, before the entry is appended to the archive.
 * See also {@link ezcArchive::append()} or {@link ezcArchive::appendToCurrent()}.
 *
 * When the ezcArchiveEntry is an entry in the archive, the {@link getPath()} method contains always an
 * relative path, and the prefix is not set.
 *
 * @package Archive
 * @version 1.4.1
 */
class ezcArchiveEntry
{
    /**
     * Is a regular file.
     */
    const IS_FILE = 0;

    /**
     * Is a hard link.
     */
    const IS_LINK = 1;

    /**
     * Is a symbolic link.
     */
    const IS_SYMBOLIC_LINK = 2;

    /**
     * Is a character device.
     */
    const IS_CHARACTER_DEVICE = 3;

    /**
     * Is a block device.
     */
    const IS_BLOCK_DEVICE = 4;

    /**
     * Is a directory.
     */
    const IS_DIRECTORY = 5;

    /**
     * Is a FIFO.
     */
    const IS_FIFO = 6;

    /**
     * Not used, is Tar specific?
     */
    const IS_RESERVED = 7;

    /**
     * Contains the file information.
     *
     * @var ezcArchiveFileStructure
     */
    protected $fileStructure;

    /**
     * The prefix of the file that may be removed from the path.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Constructs an archiveEntry from the {@link ezcArchiveFileStructure}.
     *
     * The $struct parameter contains the raw file information.
     * This class encapsulates the file information structure and provides convenient methods to retrieve
     * the information.
     *
     * @param ezcArchiveFileStructure $struct
     */
    public function __construct( ezcArchiveFileStructure $struct )
    {
        $this->fileStructure =  $struct;
    }

    /**
     * Returns true when this entry represents a directory.
     *
     * @return bool
     */
    public function isDirectory()
    {
        return ( $this->fileStructure->type == self::IS_DIRECTORY );
    }

    /**
     * Returns true when this entry represents a file.
     *
     * @return bool
     */
    public function isFile()
    {
        return ( $this->fileStructure->type == self::IS_FILE );
    }

    /**
     * Returns true when this entry represents a hard link.
     *
     * @return bool
     */
    public function isHardLink()
    {
        return ( $this->fileStructure->type == self::IS_LINK );
    }

    /**
     * Returns true when this entry represents a symbolic link.
     *
     * @return bool
     */
    public function isSymLink()
    {
        return ( $this->fileStructure->type == self::IS_SYMBOLIC_LINK );
    }

    /**
     * Returns true when this entry represents a symbolic or a hard link.
     *
     * @return bool
     */
    public function isLink()
    {
        return ( $this->isHardLink() || $this->isSymLink() );
    }

    /**
     * Returns type of the entry.
     *
     * @return int   Possible values are: {@link IS_LINK}, {@link IS_SYMBOLIC_LINK}, {@link IS_CHARACTER_DEVICE}, {@link IS_BLOCK_DEVICE},
     * {@link IS_DIRECTORY}, or {@link IS_FIFO}.
     */
    public function getType()
    {
        return $this->fileStructure->type;
    }

    /**
     * Returns the user ID of the entry.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->fileStructure->uid;
    }

    /**
     * Returns the group ID of the entry.
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->fileStructure->gid;
    }

    /**
     * Returns the complete path or path without the prefix.
     *
     * By default the full path is returned, unless the $withPrefix is set
     * to true.
     *
     * @param bool $withPrefix
     *
     * @return string
     */
    public function getPath( $withPrefix = true )
    {
        if ( $withPrefix )
        {
            return $this->fileStructure->path;
        }
        else
        {
            return $this->getPathWithoutPrefix( $this->fileStructure->path, $this->prefix );
        }
    }

    /**
     * Returns the path without the prefix.
     *
     * The path without the prefix is returned.
     * If the prefix doesn't match with the complete path, the whole path is returned.
     *
     * @param string $completePath
     * @param string $prefix
     * @return string
     */
    private function getPathWithoutPrefix( $completePath, $prefix )
    {
        $prefixLength = strlen( $prefix );

        if ( strcmp( substr( $completePath, 0,  $prefixLength ), $prefix ) == 0 )
        {
            $i = 0;
            // Check next character
            while ( $completePath[$i + $prefixLength] == "/" )
            {
                $i++;
            }

            $result = substr( $completePath, $prefixLength + $i );
            return $result;
        }
        else
        {
            // No match.
            return $completePath;
        }
    }

    /**
     * Removes the prefix from the path and clears the prefix.
     *
     * This method is useful when it comes to adding a entry to an archive
     * and the complete path to the file is no longer needed.
     */
    public function removePrefixFromPath()
    {
        $this->fileStructure->path = $this->getPathWithoutPrefix( $this->fileStructure->path, $this->prefix );
        $this->prefix = "";
    }


    /**
     * Returns the link with or without prefix.
     *
     * This method is similar to {@link getPath()}, but returns the link instead of the path.
     * If the current does not represents a link, an empty string is returned.
     * Use the {@link isLink()} method to see if the current entry is a link.
     *
     * @param bool $withPrefix
     */
    public function getLink( $withPrefix = true )
    {
        if ( $withPrefix )
        {
            return $this->fileStructure->link;
        }
        else
        {
            return $this->getPathWithoutPrefix( $this->fileStructure->link, $this->prefix );
        }
    }

    /**
     * Returns a bit mask representing the permissions of this entry.
     *
     * It returns the permissions in octal numbers as a string, for example:
     * "0000755"
     *
     * @return string
     */
    public function getPermissions()
    {
        return $this->fileStructure->mode;
    }

    /**
     * Returns the file size.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->fileStructure->size;
    }

    /**
     * Returns the modification time as a timestamp.
     *
     * @return int
     */
    public function getModificationTime()
    {
        return $this->fileStructure->mtime;
    }

    /**
     * Returns the last access time as a timestamp.
     *
     * @return int
     */
    public function getAccessTime()
    {
        return $this->fileStructure->atime;
    }

    /**
     * Returns the inode.
     *
     * @return int
     */
    public function getInode()
    {
        return $this->fileStructure->ino;
    }

    /**
     * Returns the major device number.
     *
     * @return int
     */
    public function getMajor()
    {
        return $this->fileStructure->major;
    }

    /**
     * Returns the minor device number.
     *
     * @return int
     */
    public function getMinor()
    {
        return $this->fileStructure->minor;
    }

    /**
     * Returns the device.
     *
     * FIXME DEPRECATED?
     *
     * @return int
     */
    public function getDevice()
    {
        return $this->fileStructure->device;
    }

    /**
     * Returns the permissions as a string.
     *
     * If the entry has all the permissions, it will return: "rwxrwx" Where the first three letters
     * represent the group permissions and the last three letters the user permissions.
     *
     * @return string
     */
    public function getPermissionsString()
    {
        $out = "";

        $perm = octdec( $this->getPermissions() );

        for ( $i = 6; $i >= 0; $i -= 3 )
        {
            $part = ( $perm >> $i );

            if ( $part & 4 )
            {
                $out .= "r";
            }
            else
            {
                $out .= "-";
            }

            if ( $part & 2 )
            {
                $out .= "w";
            }
            else
            {
                $out .= "-";
            }

            if ( $part & 1 )
            {
                $out .= "x";
            }
            else
            {
                $out .= "-";
            }
        }
        return $out;
    }

    /**
     * Returns the type string for the current type of the entry.
     *
     * Returns a type string for the current entry. If the entry is a:
     * - directory: "d".
     * - file: "-".
     * - symbolic link: "l".
     * - hard link: "h".
     *
     * @return string
     */
    public function getTypeString()
    {
        switch ( $this->fileStructure->type )
        {
            case self::IS_DIRECTORY:
                return "d";

            case self::IS_FILE:
                return "-";

            case self::IS_SYMBOLIC_LINK:
                return "l";

            case self::IS_LINK:
                return "h";

            default:
                return "Z";
        }
    }

    /**
     * Sets the prefix.
     *
     * @param string $prefix
     */
    public function setPrefix( $prefix )
    {
        $this->prefix = $prefix;
    }

    /**
     * Returns the prefix.
     *
     * @return string
     */
    public function getPrefix( )
    {
        return $this->prefix;
    }

    /**
     * Returns a string representing the current entry.
     *
     * @return string
     */
    public function __toString()
    {
        $out = $this->getTypeString();
        $out .= $this->getPermissionsString();

        $out .= " ";
        $out .= $this->getUserId() . " ";
        $out .= $this->getGroupId() . " ";
        $out .= str_pad( $this->getSize(), 7, " ", STR_PAD_LEFT ) . " ";
        $out .= date( "Y-m-d H:i:s ", $this->getModificationTime() );
        $out .= $this->getPath();

        $out .= ( $this->isLink() ? " -> " . $this->getLink() : "" );

        return $out;
    }

    /**
     * Create a file structure from a $file in the file system.
     *
     * @param string $file
     * @return ezcArchiveFileStructure
     */
    protected static function getFileStructureFromFile( $file )
    {
        clearstatcache();
        $stat = ( is_link( $file ) ? lstat( $file ) : stat( $file ) );
        $lstat = lstat( $file );

        // Set the file information.
        $struct = new ezcArchiveFileStructure();

        $struct->path = $file;
        $struct->gid =  $stat["gid"];
        $struct->uid =  $stat["uid"];
        $struct->mtime = $stat["mtime"];
        $struct->atime = $stat["atime"];
        $struct->mode = decoct( $stat["mode"] & ezcArchiveStatMode::S_PERM_MASK ); // First bits describe the type.
        $struct->ino = $stat["ino"];

        $struct->type = self::getLinkType( $stat );

        if ( $struct->type == ezcArchiveEntry::IS_FILE )
        {
            $struct->size = $stat["size"];
        }
        else
        {
            $struct->size = 0;
        }

        if ( $struct->type == ezcArchiveEntry::IS_SYMBOLIC_LINK )
        {
            $struct->link = readlink( $file );
        }

        $rdev = $stat["rdev"];
        if ( $rdev == -1 )
        {
            if ( $struct->type == ezcArchiveEntry::IS_BLOCK_DEVICE || $struct->type == ezcArchiveEntry::IS_CHARACTER_DEVICE)
            {
                throw new ezcArchiveException( "Cannot add a device to the TAR because the device type cannot be determined. Your system / PHP version does not support 'st_blksize'." );
            }

            $rdev = 0;
        }

        $struct->major = ( int ) ( $rdev / 256 );
        $struct->minor = ( int ) ( $rdev % 256 );

        return $struct;
    }

    /**
     * Returns one or an array of ezcArchiveEntry's from one or multiple files in the file system.
     *
     * One or multiple ezcArchiveEntry's are created upon the given files.
     * The prefix will directly set for the ezcArchiveEntry with $prefix.
     *
     * If $files contains a path to a file, then one ezcArchiveEntry will be created and returned.
     * If $files is an array of paths, then all those ezcArchiveEntry's will be created and returned in an array.
     *
     * When multiple files are given in an array, this method will search for hard links among other files in the array.
     *
     * @throws ezcArchiveEntryPrefixException if the prefix is invalid.
     *
     * @param string|array(string)  $files
     * @param string                $prefix
     * @return ezcArchiveEntry
     */
    public static function getEntryFromFile( $files, $prefix )
    {
        $isArray = true;
        if ( !is_array( $files ) )
        {
            $isArray = false;
            $files = array( $files );
        }

        $inodes = array();
        $i = 0;
        foreach ( $files as $file )
        {
            if ( !file_exists( $file ) && !is_link( $file ) )
                return false;

            $struct = self::getFileStructureFromFile( $file );

            // Check if it's a hardlink if the OS supports it, and handle it.
            if ( ezcBaseFeatures::supportsLink() )
            {
                if ( isset( $inodes[ $struct->ino ] ) )
                {
                    // Yes, it's a hardlink.
                    $struct->type = ezcArchiveEntry::IS_LINK;
                    $struct->size = 0;
                    $struct->link = $inodes[ $struct->ino ];
                }
                else
                {
                    $inodes[ $struct->ino ] = $struct->path;
                }
            }

            $entry[$i] = new ezcArchiveEntry( $struct );
            $entry[$i]->setPrefix( $prefix );

            if ( isset( $prefix ) && strlen( $prefix ) > 0 )
            {
                if ( strlen( $entry[$i]->getPath() ) == strlen( $entry[$i]->getPath( false ) ) )
                {
                    throw new ezcArchiveEntryPrefixException( $prefix, $file );
                }
            }

            $i++;
        }

        return ( $isArray ? $entry : $entry[0] );
    }

    /**
     * Returns an ezcArchiveEntry-type that corresponds to the ezcArchiveStatMode-type
     *
     * @param ezcArchiveStatMode $stat Possible values are: {@link ezcArchiveStatMode::S_IFIFO}, {@link ezcArchiveStatMode::S_IFCHR},
     *                                 {@link ezcArchiveStatMode::S_IFDIR}, {@link ezcArchiveStatMode::S_IFBLK},
     *                                 {@link ezcArchiveStatMode::S_IFREG}, or {@link ezcArchiveStatMode::S_IFLNK}.
     *
     * @return int                    Possible values are: {@link IS_LINK}, {@link IS_SYMBOLIC_LINK}, {@link IS_CHARACTER_DEVICE},
     *                                 {@link IS_BLOCK_DEVICE}, {@link IS_DIRECTORY}, or {@link IS_FIFO}.
     */
    protected static function getLinkType( $stat )
    {
        switch ( $stat["mode"] & ezcArchiveStatMode::S_IFMT )
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

            // Hardlinks are not resolved here. FIXME?
        }

        return false;
    }
}
?>
