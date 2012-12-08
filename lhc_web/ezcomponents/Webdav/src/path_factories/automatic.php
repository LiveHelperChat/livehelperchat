<?php
/**
 * File containing the ezcWebdavAutomaticPathFactory class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Path factory that automatically determines configuration.
 *
 * An object of this class is meant to be used in {@link
 * ezcWebdavTransportOptions} as the $pathFactory property. The instance of
 * {@link ezcWebdavTransport} utilizes the path factory to translate between
 * external paths/URIs and internal path representations.
 *
 * An instance of this class examines several server variables like
 * <ul>
 *     <li>$_SERVER['DOCUMENT_ROOT']</li>
 *     <li>$_SERVER['SCRIPT_FILENAME']</li>
 * </ul>
 * to determine the server configuration. It then examines incoming URIs to
 * determine which parts must be stripped an reconstructs the information when
 * serializing a path back to a URI.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavAutomaticPathFactory implements ezcWebdavPathFactory
{
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
     * Base path on the server.
     *
     * Auto-detected during __construct().
     * 
     * @var string
     */
    protected $serverFile;

    /**
     * Creates a new path factory.
     * 
     * Creates a new path factory to be used in {@link
     * ezcWebdavServerConfiguration}. This path factory automatically detects
     * information from the running web server and automatically determines the
     * suitable values for parsing paths and generating URIs.
     *
     * @return void
     */
    public function __construct()
    {
        // Get Docroot and ensure proper definition
        if ( !isset( $_SERVER['DOCUMENT_ROOT'] ) )
        {
            throw new ezcWebdavMissingServerVariableException( 'DOCUMENT_ROOT' );
        }

        // Ensure trailing slash in doc root.
        $docRoot = $_SERVER['DOCUMENT_ROOT'];
        if ( substr( $docRoot, -1, 1 ) !== '/' )
        {
            $docRoot .= '/';
        }

        // Get script filename
        if ( !isset( $_SERVER['SCRIPT_FILENAME'] ) )
        {
            throw new ezcWebdavMissingServerVariableException( 'SCRIPT_FILENAME' );
        }

        $scriptFileName = $_SERVER['SCRIPT_FILENAME'];
        
        // Get script path absolute to doc root
        $this->serverFile = '/' . str_replace(
            $docRoot, '', $scriptFileName
        );
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
        $requestPath = parse_url( $uri, PHP_URL_PATH );
        $serverBase = dirname( $this->serverFile );

        // Check for request path including index.php
        if ( strpos( $requestPath, $this->serverFile ) === 0 )
        {
            $path = substr( $requestPath, strlen( $this->serverFile ) );
        }
        // Check for request path without index.php, but with some root to cut
        else if ( $serverBase !== '/' && strpos( $requestPath, $serverBase ) === 0 )
        {
            $path = substr( $requestPath, strlen( $serverBase ) );
            $this->serverFile = $serverBase;
        }
        // Already a good path, just use it
        else
        {
            $path = $requestPath;
            $this->serverFile = '';
        }

        if ( substr( $path, -1, 1 ) === '/' )
        {
            $path = substr( $path, 0, -1 );
            $this->collectionPathes[$path] = true;
        }
        elseif ( isset( $this->collectionPathes[$path] ) )
        {
            unset( $this->collectionPathes[$path] );
        }

        return ( is_string( $path ) && $path !== '' ? $path : '/' );
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
        $proto = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http' );
        $port = ( $proto === 'http' && $_SERVER['SERVER_PORT'] == 80 || $proto === 'https' && $_SERVER['SERVER_PORT'] == 443 )
            ? null
            : $_SERVER['SERVER_PORT'];

        return $proto . '://' . $_SERVER['SERVER_NAME'] 
             . ( $port !== null ? ':' . $port : '' )
             . $this->serverFile
             . $path
             . ( isset( $this->collectionPathes[$path] ) ? '/' : '' );
    }
}

?>
