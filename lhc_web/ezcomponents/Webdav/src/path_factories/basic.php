<?php
/**
 * File containing the ezcWebdavBasicPathFactory class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Basic path factory.
 *
 * An object of this class is meant to be used in {@link
 * ezcWebdavTransportOptions} as the $pathFactory property. The instance of
 * {@link ezcWebdavTransport} utilizes the path factory to translate between
 * external paths/URIs and internal path representations.
 *
 * This simple implementation of a path factory expects the base URI it should
 * be working on as the constructor parameter. It will translate all incoming
 * URIs to internal paths and the other way round based on this URI.
 *
 * You may want to provide custome implementations for different mappings. The
 * {@link ezcWebdavAutomaticPathFactory} is a more advanced implementation,
 * which automatically "guesses" the server configuration and should be
 * suitable for almost every case.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavBasicPathFactory implements ezcWebdavPathFactory
{
    /**
     * Result of parse_url() for the {@link $baseUri} submitted to the ctor.
     * 
     * @var array(string=>string)
     */
    protected $baseUriParts;

    /**
     * Caches paths that are a collection.
     *
     * Those will get a '/' appended on re-serialization. Works only if they
     * had been unserialized before.
     *
     * @var array(string=>bool)
     *
     * @apichange This property will be renamed to $collectionPaths in the next
     *            major release.
     */
    protected $collectionPathes = array();
    
    /**
     * Creates a new path factory.
     *
     * Creates a new object to parse URIs to local paths and vice versa. The
     * URL given as a parameter is used to strip URL and path parts from
     * incoming URIs {@link parseUriToPath()} and to add the specific parts to
     * outgoing ones {@link generateUriFromPath()}.
     * 
     * @param string $baseUri 
     * @return void
     */
    public function __construct( $baseUri = '' )
    {
        // Fix potentially incorrect $baseUri, since we need the first '/' or
        // the path.
        if ( substr( $baseUri, -1, 1 ) === '/' )
        {
            $baseUri = substr( $baseUri, 0, -1 );
        }

        if ( ( $this->baseUriParts = @parse_url( $baseUri ) ) === false )
        {
            throw new ezcWebdavBrokenBaseUriException( $baseUri );
        }
        
        // Sanity checks for essential elements
        if ( !isset( $this->baseUriParts['scheme'] ) )
        {
            throw new ezcWebdavBrokenBaseUriException(
                $baseUri, 'Missing URI scheme.'
            );
        }
        if ( !isset( $this->baseUriParts['host'] ) )
        {
            throw new ezcWebdavBrokenBaseUriException(
                $baseUri, 'Missing URI host.'
            );
        }
    }

    /**
     * Parses the given URI to a path suitable to be used in the backend.
     *
     * This method retrieves a URI (either full qualified or relative) and
     * translates it into a local path, which can be understood by the {@link
     * ezcWebdavBackend} instance used in the {@link ezcWebdavServer}.
     *
     * A locally understandable path MUST NOT contain a trailing slash, but
     * MUST always contain a starting slash. For the root URI the path "/" MUST
     * be used.
     *
     * @param string $uri
     * @return string
     */
    public function parseUriToPath( $uri )
    {
        $requestPath = parse_url( trim( $uri ), PHP_URL_PATH );

        $requestPath = str_replace( '//', '/', $requestPath );

        if ( substr( $requestPath, -1, 1 ) === '/' )
        {
            $requestPath = substr( $requestPath, 0, -1 );
            $this->collectionPathes[substr( $requestPath, ( isset( $this->baseUriParts['path'] ) ? strlen( $this->baseUriParts['path'] ) : 0 ) )] = true;
        }
        else
        {
            // @todo Some clients first send with / and then discover it is not a resource
            // therefore the upper todo might be refined.
            if ( isset( $this->collectionPathes[substr( $requestPath, ( isset( $this->baseUriParts['path'] ) ? strlen( $this->baseUriParts['path'] ) : 0 ) )] ) )
            {
                unset( $this->collectionPathes[substr( $requestPath, ( isset( $this->baseUriParts['path'] ) ? strlen( $this->baseUriParts['path'] ) : 0 ) )] );
            }
        }
        $requestPath = substr( $requestPath, ( isset( $this->baseUriParts['path'] ) ? strlen( $this->baseUriParts['path'] ) : 0 ) );

        // Ensure starting slash for root node
        if ( !$requestPath )
        {
            $requestPath = '/';
        }

        // Attach fragment to avoid performing operations occasionally
        if ( ( $frag = parse_url( $uri, PHP_URL_FRAGMENT ) ) !== null )
        {
            $requestPath .= "#$frag";
        }

        return $requestPath;
    }

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
    public function generateUriFromPath( $path )
    {
        return $this->baseUriParts['scheme'] 
             . '://' 
             . ( isset( $this->baseUriParts['user'] ) ? $this->baseUriParts['user'] : '' )
             . ( isset( $this->baseUriParts['pass'] ) ? ':' . $this->baseUriParts['pass'] : '' )
             . ( isset( $this->baseUriParts['user'] ) || isset( $this->baseUriParts['pass'] ) ? '@' : '' )
             . $this->baseUriParts['host']
             . ( isset( $this->baseUriParts['path'] ) ? $this->baseUriParts['path'] : '' )
             . trim( $path ) . ( isset( $this->collectionPathes[$path] ) ? '/' : '' )
             . ( isset( $this->baseUriParts['query'] ) ? '?' . $this->baseUriParts['query'] : '' )
             . ( isset( $this->baseUriParts['fragment'] ) ? '#' . $this->baseUriParts['fragment'] : '' );
    }
}

?>
