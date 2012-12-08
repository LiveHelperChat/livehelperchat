<?php
/**
 * File containing the ezcWebdavMemoryBackend class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Backend that only resides in memory.
 *
 * Memory backend to serve some virtual content tree, offering options to cause
 * failures in operations, mainly for testing the webdav server.
 *
 * The content of the backend is constructed from a multidimentional array
 * structure representing the collections and files. The metadata may only be
 * set by appropriate requests to the backend. No information is stored
 * anywhere, so that every reinitialisations gives you a fresh backend.
 *
 * <code>
 *  $backend = new ezcWebdavMemoryBackend();
 *  $backend->addContents(
 *      array(
 *          'foo' => 'bar', // File with content "bar"
 *          'bar' => array( // Collection "bar"
 *              'blubb' => 'Some more content.'
 *                  // File bar/blubb with some more content
 *          ),
 *      )
 *  );
 * </code>
 *
 * This backend does not implement any special features to test the servers
 * capabilities to work with those features.
 *
 * @version 1.1.4
 * @package Webdav
 * @access private
 */
class ezcWebdavMemoryBackend extends ezcWebdavSimpleBackend implements ezcWebdavLockBackend
{
    /**
     * Options of the memory backend
     * 
     * @var ezcWebdavMemoryBackendOptions
     */
    protected $options;

    /**
     * Content structure of memory backend
     * 
     * @var array
     */
    protected $content = array(
        '/' => array(),
    );

    /**
     * Properties for collections and resources.
     *
     * They are stored in an array of the following form reusing the initial
     * content example:
     *
     *  array(
     *      '/foo' => array(
     *          'property name' => 'property value',
     *      ),
     *      '/bar' => array(),
     *      '/bar/blubb' => array(),
     *      ...
     *  )
     * 
     * @var array
     */
    protected $props = array();

    /**
     * Indicates wheather to fake live properties.
     * 
     * @var bool
     */
    protected $fakeLiveProperties;

    /**
     * Construct backend from a given path.
     * 
     * @param bool $fakeLiveProperties
     * @return void
     */
    public function __construct( $fakeLiveProperties = true )
    {
        $this->options = new ezcWebdavMemoryBackendOptions();

        $this->fakeLiveProperties = $fakeLiveProperties;

        // Initialize properties for root
        if ( $fakeLiveProperties )
        {
            $this->props['/'] = $this->initializeProperties( '/', true );
        }
    }

    /**
     * Acquire a backend lock.
     *
     * Locks the complete backend using the lock file specified in {@link
     * ezcWebdavMemoryBackendOptions->$lockFile}.
     *
     * @param int $waitTime Microseconds.
     * @param int $timeout Microseconds.
     * @return void
     */
    public function lock( $waitTime, $timeout )
    {
        $lockFile = $this->options->lockFile;
        
        $lockStart = microtime( true );

        // fopen in mode 'x' will only open the file, if it does not exist yet.
        // Even this is is expected it will throw a warning, if the file
        // exists, which we need to silence using the @
        while ( ( $fp = @fopen( $lockFile, 'x' ) ) === false )
        {
            // This is untestable.
            if ( microtime( true ) - $lockStart > $timeout )
            {
                // Release timed out lock
                unlink( $lockFile );
                $lockStart = microtime( true );
            }
            else
            {
                usleep( $waitTime );
            }
        }

        // Store random bit in file ... the microtime for example - might prove
        // useful some time.
        fwrite( $fp, microtime() );
        fclose( $fp );
    }

    /**
     * Release the backend lock.
     *
     * Releases the lock acquired by {@link lock()}.
     * 
     * @return void
     */
    public function unlock()
    {
        // Silence since lock maybe released if request processing takes too
        // long
        @unlink( $this->options->lockFile );
    }

    /**
     * Offer access to some of the server properties.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If the property $name is not defined
     * @param string $name 
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'options':
                return $this->$name;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name
     * @param mixed $value
     * @return void
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'options':
                if ( ! $value instanceof ezcWebdavMemoryBackendOptions )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcWebdavMemoryBackendOptions' );
                }

                $this->$name = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Return an initial set of properties for resources and collections.
     *
     * The second parameter indicates wheather the given resource is a
     * collection. The returned properties are used to initialize the property
     * arrays for the given content.
     * 
     * @param string $name
     * @param bool $isCollection
     * @return array
     *
     * @access protected
     */
    public function initializeProperties( $name, $isCollection = false )
    {
        if ( $this->fakeLiveProperties )
        {
            $propertyStorage = new ezcWebdavBasicPropertyStorage();

            // Add default creation date
            $propertyStorage->attach(
                new ezcWebdavCreationDateProperty( new ezcWebdavDateTime( '@1054034820' ) )
            );

            // Define default display name
            $propertyStorage->attach(
                new ezcWebdavDisplayNameProperty( basename( urldecode( $name ) ) )
            );

            // Define default language
            $propertyStorage->attach(
                new ezcWebdavGetContentLanguageProperty( array( 'en' ) )
            );

            // Define default content type
            $propertyStorage->attach(
                new ezcWebdavGetContentTypeProperty( 
                    $isCollection ? 'httpd/unix-directory' : 'application/octet-stream'
                )
            );

            // Define default ETag
            $propertyStorage->attach(
                new ezcWebdavGetEtagProperty( $this->getETag( $name ) )
            );

            // Define default modification time
            $propertyStorage->attach(
                new ezcWebdavGetLastModifiedProperty( new ezcWebdavDateTime( '@1124118780' ) )
            );

            // Define content length if node is a resource.
            $propertyStorage->attach(
                new ezcWebdavGetContentLengthProperty(
                    $isCollection ?
                        ezcWebdavGetContentLengthProperty::COLLECTION :
                        (string) strlen( $this->content[$name] )
                )
            );

            $propertyStorage->attach(
                new ezcWebdavResourceTypeProperty(
                    ( $isCollection === true ? 
                        ezcWebdavResourceTypeProperty::TYPE_COLLECTION : 
                        ezcWebdavResourceTypeProperty::TYPE_RESOURCE
                    )
                )
            );
        }
        else
        {
            $propertyStorage = new ezcWebdavBasicPropertyStorage();
        }

        return $propertyStorage;
    }

    /**
     * Clones the given $fromStorage for $toPath.
     *
     * Initializes a new property storage for $toPath with new live properties 
     * and clones all non exsitent properties from $fromStorage to it.
     * 
     * @param string $toPath 
     * @param bool $isCollection 
     * @param ezcWebdavBasicPropertyStorage $fromStorage 
     * @return void
     */
    private function cloneProperties( $toPath, $isCollection, ezcWebdavBasicPropertyStorage $fromStorage )
    {
        $toStorage = $this->initializeProperties( $toPath, $isCollection );
        
        foreach ( $fromStorage as $prop )
        {
            if ( !$toStorage->contains( $prop->name, $prop->namespace ) )
            {
                $toStorage->attach( clone $prop );
            }
        }
        $this->props[$toPath] = $toStorage;
    }

    /**
     * Overwrites ETag generation from simple backend.
     *
     * Generates an ETag based on $path and the content of $path (if available
     * and not a collection).
     * 
     * @param string $path 
     * @return string
     */
    protected function getETag( $path )
    {
        return ( md5( $path ) );
    }

    /**
     * Read valid data from given content array and initialize property
     * storage.
     * 
     * @param array $contents 
     * @param string $path
     * @return void
     */
    public function addContents( array $contents, $path = '/' )
    {
        foreach ( $contents as $name => $content )
        {
            if ( !is_string( $name ) )
            {
                // Ignore elements which do not have a string key
                continue;
            }

            // Full path to resource
            $resourcePath = $path . $name;

            if ( is_array( $content ) )
            {
                // Content is a collection
                $this->content[$resourcePath] = array();
                $this->props[$resourcePath] = $this->initializeProperties(
                    $resourcePath,
                    true
                );

                // Recurse
                $this->addContents( $content, $resourcePath . '/' );
            }
            elseif ( is_string( $content ) )
            {
                // Content is a file
                $this->content[$resourcePath] = $content;
                $this->props[$resourcePath] = $this->initializeProperties(
                    $resourcePath
                );
            }
            else
            {
                // Ignore everything else...
                continue;
            }

            // Add contents to parent directory
            $parent = ( $path === '/' ? '/' : substr( $path, 0, -1 ) );
            $this->content[$parent][] = $resourcePath;
        }
    }

    /**
     * Create a new collection.
     *
     * Creates a new collection at the given path.
     * 
     * @param string $path 
     * @return void
     */
    protected function createCollection( $path )
    {
        // Create collection
        $this->content[$path] = array();

        // Add collection to parent node
        $this->content[dirname( $path )][] = $path;

        // Set initial metadata for collection
        $this->props[$path] = $this->initializeProperties( $path, true );
    }

    /**
     * Create a new resource.
     *
     * Creates a new resource at the given path, optionally with the given
     * content.
     * 
     * @param string $path 
     * @param string $content 
     * @return void
     */
    protected function createResource( $path, $content = null )
    {
        // Create resource
        $this->content[$path] = $content;

        // Add resource to parent node
        $this->content[dirname( $path )][] = $path;

        // Set initial metadata for collection
        $this->props[$path] = $this->initializeProperties( $path, false );
    }

    /**
     * Set contents of a resource.
     *
     * Change the contents of the given resource to the given content.
     * 
     * @param string $path 
     * @param string $content 
     * @return void
     */
    protected function setResourceContents( $path, $content )
    {
        $this->content[$path] = $content;
    }

    /**
     * Get contents of a resource.
     * 
     * @param string $path 
     * @return string
     */
    protected function getResourceContents( $path )
    {
        return $this->content[$path];
    }

    /**
     * Manually set a property on a resource to request it later.
     * 
     * @param string $resource 
     * @param ezcWebdavProperty $property
     * @return bool
     */
    public function setProperty( $resource, ezcWebdavProperty $property )
    {
        // Live properties may not be updated by a client.
        if ( $this->options->failingOperations & ezcWebdavMemoryBackendOptions::REQUEST_PROPPATCH )
        {
            return false;
        }

        // Bail out, if the resource is not known yet.
        if ( !array_key_exists( $resource, $this->props ) )
        {
            return false;
        }

        $this->props[$resource]->attach( $property );
        return true;
    }

    /**
     * Manually remove a property from a resource.
     * 
     * @param string $resource 
     * @param ezcWebdavProperty $property
     * @return bool
     */
    public function removeProperty( $resource, ezcWebdavProperty $property )
    {
        // Live properties may not be removed.
        if ( $property instanceof ezcWebdavLiveProperty )
        {
            return false;
        }

        $this->props[$resource]->detach( $property->name, $property->namespace );
        return true;
    }

    /**
     * Reset property storage for a resource.
     * 
     * @param string $resource 
     * @param ezcWebdavPropertyStorage $properties
     * @return bool
     */
    public function resetProperties( $resource, ezcWebdavPropertyStorage $properties )
    {
        $this->props[$resource] = $properties;
    }

    /**
     * Manually get a property on a resource.
     * 
     * Get the property with the given name from the given resource. You may
     * optionally define a namespace to receive the property from.
     *
     * @param string $resource 
     * @param string $propertyName 
     * @param string $namespace 
     * @return ezcWebdavProperty
     */
    public function getProperty( $resource, $propertyName, $namespace = 'DAV:' )
    {
        return $this->props[$resource]->get( $propertyName, $namespace );
    }

    /**
     * Manually get a property on a resource.
     * 
     * Get all properties for the given resource as a {@link
     * ezcWebdavPropertyStorage}
     *
     * @param string $resource 
     * @return ezcWebdavBasicPropertyStorage
     */
    public function getAllProperties( $resource )
    {
        return $this->props[$resource];
    }

    /**
     * Copy resources recursively from one path to another.
     *
     * Returns an array with {@link ezcWebdavErrorResponse}s for all subtree,
     * where the copy operation failed. Errors subsequent nodes in a subtree
     * should be ommitted.
     *
     * If an empty array is return, the operation has been completed
     * successfully.
     * 
     * @param string $fromPath 
     * @param string $toPath 
     * @param int $depth
     * @return array(ezcWebdavErrorResponse)
     */
    protected function performCopy( $fromPath, $toPath, $depth = ezcWebdavRequest::DEPTH_INFINITY )
    {
        $causeErrors = (bool) ( $this->options->failingOperations & ( ezcWebdavMemoryBackendOptions::REQUEST_COPY | ezcWebdavMemoryBackendOptions::REQUEST_MOVE ) );
        $errors = array();
        
        if ( !is_array( $this->content[$fromPath] ) ||
             ( is_array( $this->content[$fromPath] ) && ( $depth === ezcWebdavRequest::DEPTH_ZERO ) ) )
        {
            // Copy a resource, or a collection, but the depth header told not
            // to recurse into collections
            if ( $causeErrors && preg_match( $this->options->failForRegexp, $fromPath ) > 0 )
            {
                // Completely abort with error
                return array( ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_423,
                    $fromPath
                ) );
            }
            if ( $causeErrors && preg_match( $this->options->failForRegexp, $toPath ) > 0 )
            {
                // Completely abort with error
                return array( ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_412,
                    $toPath
                ) );
            }

            // Perform copy operation
            if ( is_array( $this->content[$fromPath] ) )
            {
                // Create a new empty collection
                $this->content[$toPath] = array();
            }
            else
            {
                // Copy file content
                $this->content[$toPath] = $this->content[$fromPath];
            }

            // Copy properties
            $this->cloneProperties(
                $toPath,
                is_array( $this->content[$toPath] ),
                $this->props[$fromPath]
            );

            // Add to parent node
            $this->content[dirname( $toPath )][] = $toPath;
        }
        else
        {
            // Copy a collection
            $errnousSubtrees = array();

            // Array of copied collections, where the child names are required
            // to be modified depending on the success of the copy operation.
            $copiedCollections = array();

            // Check all nodes, if they math the fromPath
            foreach ( $this->content as $resource => $content )
            {
                if ( strpos( $resource, $fromPath ) !== 0 )
                {
                    // This resource is not affected by the copy operation
                    continue;
                }

                // Check if this resource should be skipped, because
                // already one of the parent nodes caused an error.
                foreach ( $errnousSubtrees as $subtree )
                {
                    if ( strpos( $resource, $subtree ) )
                    {
                        // Skip resource, then.
                        continue 2;
                    }
                }

                // Check if this resource should cause an error
                if ( $causeErrors && preg_match( $this->options->failForRegexp, $resource ) )
                {
                    // Cause an error and skip resource
                    $errors[] = new ezcWebdavErrorResponse(
                        ezcWebdavResponse::STATUS_423,
                        $resource
                    );
                    continue;
                }

                // To actually perform the copy operation, modify the
                // destination resource name
                $newResourceName = preg_replace( '(^' . preg_quote( $fromPath ) . ')', $toPath, $resource );
                
                // Check if this resource should cause an error
                if ( $causeErrors && preg_match( $this->options->failForRegexp, $newResourceName ) )
                {
                    // Cause an error and skip resource
                    $errors[] = new ezcWebdavErrorResponse(
                        ezcWebdavResponse::STATUS_412,
                        $newResourceName
                    );
                    continue;
                }
                
                // Add collection to collection child recalculation array
                if ( is_array( $this->content[$resource] ) )
                {
                    $copiedCollections[] = $newResourceName;
                }

                // Actually copy
                $this->content[$newResourceName] = $this->content[$resource];

                // Copy properties
                $this->cloneProperties(
                    $newResourceName,
                    is_array( $this->content[$resource] ),
                    $this->props[$resource]
                );

                // Add to parent node
                $this->content[dirname( $newResourceName )][] = $newResourceName;
            }

            // Iterate over all copied collections and update the child
            // references
            foreach ( $copiedCollections as $collection )
            {
                foreach ( $this->content[$collection] as $nr => $child )
                {
                    foreach ( $errnousSubtrees as $subtree )
                    {
                        if ( strpos( $child, $subtree ) )
                        {
                            // If child caused an error, it has not been
                            // copied, so we remove it.
                            unset( $this->content[$collection][$nr] );
                            continue 2;
                        }
                    }

                    // Also remove all references to old children, new children
                    // have already been added during the last step.
                    if ( preg_match( '(^' . preg_quote( $fromPath ) . ')', $child ) )
                    {
                        unset( $this->content[$collection][$nr] );
                    }
                }

                $this->content[$collection] = array_values( $this->content[$collection] );
            }
        }

        return $errors;
    }

    /**
     * Delete everything below this path.
     *
     * Returns an error response if the deletion failed, and null on success.
     * 
     * @param string $path 
     * @return ezcWebdavErrorResponse
     */
    protected function performDelete( $path )
    {
        // Check if any errors would occur during deletion process
        $error = array();
        foreach ( $this->content as $name => $content )
        {
            if ( strpos( $name, $path ) === 0 && ( substr( $name, strlen( $path ), 1 ) === '/' || $name === $path ) )
            {
                // Check if we want to cause some errors here.
                if ( $this->options->failingOperations & ezcWebdavMemoryBackendOptions::REQUEST_DELETE && preg_match( $this->options->failForRegexp, $name ) > 0 )
                {
                    $error[] = new ezcWebdavErrorResponse(
                        ezcWebdavResponse::STATUS_423,
                        $name
                    );
                }
            }
        }

        // If errors occured, return them
        if ( count( $error ) )
        {
            return new ezcWebdavMultistatusResponse(
                $error
            );
        }

        // Remove all content nodes starting with requested path
        foreach ( $this->content as $name => $content )
        {
            if ( strpos( $name, $path ) === 0 && ( substr( $name, strlen( $path ), 1 ) === '/' || $name === $path ) )
            {
                unset( $this->content[$name] );
                unset( $this->props[$name] );
            }
        }

        // Remove parent node assignement to removed node
        $id = array_search( $path, $this->content[$parent = dirname( $path )] );
        if ( $id !== false )
        {
            unset( $this->content[$parent][$id] );
            $this->content[$parent] = array_values( $this->content[$parent] );
        }

        return null;
    }

    /**
     * Check if node exists.
     *
     * Check if a node exists with the given path.
     * 
     * @param string $path 
     * @return bool
     *
     * @access protected
     */
    public function nodeExists( $path )
    {
        return isset( $this->content[$path] );
    }

    /**
     * Check if node is a collection.
     *
     * Check if the node behind the given path is a collection.
     * 
     * @param string $path 
     * @return bool
     *
     * @access protected
     */
    public function isCollection( $path )
    {
        return $this->nodeExists( $path ) && is_array( $this->content[$path] );
    }

    /**
     * Get members of collection.
     *
     * Returns an array with the members of the collection given by the path of
     * the collection.
     *
     * The returned array holds elements which are either ezcWebdavCollection,
     * or ezcWebdavResource.
     * 
     * @param string $path 
     * @return array
     */
    protected function getCollectionMembers( $path )
    {
        $contents = array();

        foreach ( $this->content[$path] as $child )
        {
            if ( is_array( $this->content[$child] ) )
            {
                // Add collection without any children
                $contents[] = new ezcWebdavCollection(
                    $child
                );
            }
            else
            {
                // Add files without content
                $contents[] = new ezcWebdavResource(
                    $child
                );
            }
        }

        return $contents;
    }

    /**
     * Clones the memory backend deeply. 
     * 
     * @return void
     */
    public function __clone()
    {
        foreach ( $this->props as $path => $propStorage )
        {
            $this->props[$path] = clone $propStorage;
        }
    }
}

?>
