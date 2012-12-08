<?php
/**
 * File containing the ezcArchiveUnknownTypeException class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown when encountering an archive of an unknow type.
 *
 * @package Archive
 * @version 1.4.1
 */
class ezcArchiveUnknownTypeException extends ezcArchiveException
{
    /**
     * Constructs a new unknown type exception for the specified archive.
     *
     * @param string $archiveName
     */
    public function __construct( $archiveName )
    {
        parent::__construct( "The type of the archive '{$archiveName}' cannot be determined." );
    }
}
?>
