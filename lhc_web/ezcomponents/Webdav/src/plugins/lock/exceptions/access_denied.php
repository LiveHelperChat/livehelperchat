<?php
/**
 * File containing the ezcWebdavLockAccessDeniedException class.
 * 
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Exception thrown if access was denied during lock violation checks.
 *
 * This exception is thrown while extracting the properties needed to check
 * lock violations. It is not bubbled up the server, but handled in {@link
 * ezcWebdavLockTools::checkViolations()}.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockAccessDeniedException extends ezcWebdavException
{
    /**
     * Creates a new lock access denied exception.
     *
     * Access was denied to $node, while checking lock conditions.
     * 
     * @param ezcWebdavResource|ezcWebdavCollection $node 
     */
    public function __construct( $node )
    {
        parent::__construct(
            "Access denied to '{$node->path}'."
        );
    }
}

?>
