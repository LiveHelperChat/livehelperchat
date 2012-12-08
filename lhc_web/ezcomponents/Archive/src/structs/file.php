<?php
/**
 * File contains the ezcArchiveFileStructure class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcArchiveFileStructure class represents a data structure which contains file information.
 *
 * This class is used as an structure in order to prevent using an hash (or array).
 * {@link ezcArchiveEntry} encapsulates this class and has convenient methods to retrieve the information.
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
class ezcArchiveFileStructure extends ezcBaseStruct
{
    /**
     * The file path.
     *
     * @var string
     */
    public $path;

    /**
     * The permissions of the entry.
     *
     * @var int
     */
    public $mode;

    /**
     * The user ID.
     *
     * @var int
     */
    public $uid;

    /**
     * The group ID.
     *
     * @var int
     */
    public $gid;

    /**
     * The user name (only for some supported formats).
     *
     * @var string
     */
    public $userName = null;

    /**
     * The group name (only for some supported formats).
     *
     * @var string
     */
    public $groupName = null;

    /**
     * Last modification time timestamp.
     *
     * @var int
     */
    public $mtime;

    /**
     * Last access time timestamp.
     *
     * @var int
     */
    public $atime = false;

    /**
     * Specifies the type of the entry.
     *
     * @var int  Possible values: {@link ezcArchiveEntry::IS_LINK}, {@link ezcArchiveEntry::IS_SYMBOLIC_LINK},
     *           {@link ezcArchiveEntry::IS_CHARACTER_DEVICE}, {@link ezcArchiveEntry::IS_BLOCK_DEVICE},
     *           {@link ezcArchiveEntry::IS_DIRECTORY}, or {@link ezcArchiveEntry::IS_FIFO}.
     */
    public $type;

    /**
     * The link target.
     *
     * This value is only valid when the {@link $type} indicates an actual link.
     *
     * @var string
     */
    public $link;

    /**
     * The file size in bytes.
     *
     * $var int
     */
    public $size;

    /**
     * Inode number of the file.
     *
     * $var int
     */
    public $ino;

    /**
     * Major device number.
     *
     * $var int
     */
    public $major;

    /**
     * Minor device number.
     *
     * $var int
     */
    public $minor;
}
?>
