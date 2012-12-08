<?php
/**
 * File containing the ezcArchiveStatMode class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcArchiveStatMode class stores the stat-mode constant values.
 *
 * Compare the bits from the "mode" array element from {@link http://www.php.net/stat}.
 * For example to get the file permissions in an octal number:
 * <code>
 * $stat = stat( "/tmp/myfile.txt" );
 * $perm = decoct( $stat["mode"] & ezcArchiveStatMode::S_PERM_MASK );
 * </code>
 *
 * To see if the file is a directory, the following code can be used:
 * <code>
 * $stat = stat( "/tmp/myfile.txt" );
 * $isDirectory = ( ( $stat["mode"] & S_FMT ) == ezcArchiveStatMode::S_IFDIR );
 * </code>
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
class ezcArchiveStatMode
{
    /**
     * Type of the file.
     */
    const S_IFMT = 0170000;

    /**
     * Named pipe (fifo).
     */
    const S_IFIFO = 0010000;

    /**
     * character special.
     */
    const S_IFCHR = 0020000;

    /**
     * Directory
     */
    const S_IFDIR = 0040000;

    /**
     * block special
     */
    const S_IFBLK = 0060000;

    /**
     * regular file
     */
    const S_IFREG = 0100000;

    /**
     * Symbolic link
     */
    const S_IFLNK = 0120000;

    /**
     * Socket
     */
    const S_IFSOCK = 0140000;

    /**
     * Whiteout
     */
    const S_IFWHT = 0160000;

    /**
     * Permission mask
     */
    const S_PERM_MASK = 07777;
}
?>
