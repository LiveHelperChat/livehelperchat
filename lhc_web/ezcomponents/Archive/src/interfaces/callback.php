<?php
/**
 * File containing the ezcArchiveCallback class
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing a basic implementation of the callback class to be called
 * through extract.
 *
 * @package Archive
 * @version 1.4.1
 */
abstract class ezcArchiveCallback
{
    /**
     * Callback that's called for every file creation.
     *
     * The callback implementation is allowed to modify the $permissions,
     * $userId and $groupId. The latter two however might not have any
     * effect depending on which user and group the code runs at.
     *
     * @param string $fileName
     * @param int    $permissions
     * @param string $userId
     * @param string $groupId
     */
    function createFileCallback( $fileName, &$permissions, &$userId, &$groupId )
    {
    }

    /**
     * Callback that's called for every directory creation.
     *
     * The callback implementation is allowed to modify the $permissions,
     * $userId and $groupId. The latter two however might not have any
     * effect depending on which user and group the code runs at. You also need
     * to be aware that subsequent files might be put into this directory, and
     * bad things might happen when they can not be created there due to
     * operating system level restrictions.
     *
     * @param string $dirName
     * @param int    $permissions
     * @param string $userId
     * @param string $groupId
     */
    function createDirectoryCallback( $dirName, &$permissions, &$userId, &$groupId )
    {
    }
}
?>
