<?php
/**
 * File containing the ezcWebdavPathFactory interface.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Basic path factory interface.
 *
 * An object that implements this interface is meant to be used in {@link
 * ezcWebdavServerConfiguration} as the $pathFactory property. The instance of
 * {@link ezcWebdavTransport} utilizes the path factory to translate between
 * external paths/URIs and paths that are usable with the a {@link
 * ezcWebdavBackend}.
 *
 * You may want to provide custome implementations for different mappings.
 *
 * @see ezcWebdavBasicPathFactory
 * @see ezcWebdavAutomaticPathFactory
 *
 * @version 1.1.4
 * @package Webdav
 */
interface ezcWebdavPathFactory
{
    /**
     * Parses the given URI to a path suitable to be used in the backend.
     *
     * This method retrieves a URI (either full qualified or relative) and
     * translates it into a local path, which can be understood by the {@link
     * ezcWebdavBackend} instance used in the {@link ezcWebdavServer}.
     *
     * @param string $uri
     * @return string
     */
    public function parseUriToPath( $uri );

    /**
     * Generates a URI from a local path.
     *
     * This method receives a local $path string, representing a resource in
     * the {@link ezcWebdavBackend} and translates it into a full qualified URI
     * to be used as external reference.
     * 
     * @param string $path 
     * @return string
     */
    public function generateUriFromPath( $path );
}

?>
