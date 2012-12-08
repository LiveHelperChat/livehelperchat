<?php
/**
 * File containing the ezcWebdavFileBackend class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * File system based backend.
 *
 * This backend serves WebDAV resources from a directory structure in the file
 * system. It simply handles directories as collection resources and files as
 * non-collection resources. The path to server resources from is defined
 * during construction.
 *
 * <code>
 *  $backend = new ezcWebdavFileBackend(
 *      'directory/'
 *  );
 * </code>
 *
 * Live properties are partly determined from the file systems itself (like
 * {@link ezcWebdavGetContentLengthProperty}), others need to be stored like
 * dead properties. This backend uses a special path for each resource to store
 * this information in its XML representation.
 *
 * @version 1.1.4
 * @package Webdav
 * @mainclass
 */
class ezcWebdavFileBackend extends ezcWebdavSimpleBackend implements ezcWebdavLockBackend
{
    /**
     * Options.
     * 
     * @var ezcWebdavFileBackendOptions
     */
    protected $options;

    /**
     * Root directory to serve content from. All paths are seen relatively to this one.
     * 
     * @var string
     */
    protected $root;

    /**
     * Keeps track of the lock level.
     *
     * Each time the lock() method is called, this counter is raised by 1. if
     * it was 0 before, the actual locking mechanism gets into action,
     * otherwise just the counter is raised. The lock is physically only freed,
     * if this counter is 0.
     *
     * This mechanism allows nested locking, as it is necessary, if the lock
     * plugin locks this backend external, but interal locking needs still to
     * be supported.
     * 
     * @var int
     */
    protected $lockLevel = 0;

    /**
     * Names of live properties from the DAV: namespace which will be handled
     * live, and should not be stored like dead properties.
     * 
     * @var array(int=>string)
     */
    protected $handledLiveProperties = array( 
        'getcontentlength', 
        'getlastmodified', 
        'creationdate', 
        'displayname', 
        'getetag', 
        'getcontenttype', 
        'resourcetype',
        'supportedlock',
        'lockdiscovery',
    );

    /**
     * Creates a new backend instance.
     * 
     * Creates a new backend to server WebDAV content from the file system path
     * identified by $root. If the given path does not exist or is not a
     * directory, an exception will be thrown.
     *
     * @param string $root 
     * @return void
     *
     * @throws ezcBaseFileNotFoundException
     *         if the given $root does not exist or is not a directory.
     * @throws ezcBaseFilePermissionException
     *         if the given $root is not readable.
     */
    public function __construct( $root )
    {
        if ( !is_dir( $root ) )
        {
            throw new ezcBaseFileNotFoundException( $root );
        }

        if ( !is_readable( $root ) )
        {
            throw new ezcBaseFilePermissionException( $root, ezcBaseFileException::READ );
        }

        $this->root = realpath( $root );
        $this->options = new ezcWebdavFileBackendOptions();
    }

    /**
     * Locks the backend.
     *
     * Tries to lock the backend. If the lock is already owned by this process,
     * locking is successful. If $timeout is reached before a lock could be
     * acquired, an {@link ezcWebdavLockTimeoutException} is thrown. Waits
     * $waitTime microseconds between attempts to lock the backend.
     * 
     * @param int $waitTime 
     * @param int $timeout 
     * @return void
     */
    public function lock( $waitTime, $timeout )
    {
        // Check and raise lockLevel counter
        if ( $this->lockLevel > 0 )
        {
            // Lock already acquired
            ++$this->lockLevel;
            return;
        }

        $lockStart = microtime( true );

        $lockFileName = $this->root . '/' . $this->options->lockFileName;

        if ( is_file( $lockFileName ) && !is_writable( $lockFileName )
             || !is_file( $lockFileName ) && !is_writable(dirname( $lockFileName ) ) )
        {
            throw new ezcBaseFilePermissionException(
                $lockFileName,
                ezcBaseFileException::WRITE,
                'Cannot be used as lock file.'
            );
        }

        // fopen in mode 'x' will only open the file, if it does not exist yet.
        // Even this is is expected it will throw a warning, if the file
        // exists, which we need to silence using the @
        while ( ( $fp = @fopen( $lockFileName, 'x' ) ) === false )
        {
            // This is untestable.
            if ( microtime( true ) - $lockStart > $timeout )
            {
                // Release timed out lock
                unlink( $lockFileName );
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

        // Add first lock
        ++$this->lockLevel;
    }

    /**
     * Removes the lock.
     * 
     * @return void
     */
    public function unlock()
    {
        if ( --$this->lockLevel === 0 )
        {
            // Remove the lock file
            $lockFileName = $this->root . '/' . $this->options->lockFileName;
            unlink( $lockFileName );
        }
    }


    /**
     * Property get access.
     * Simply returns a given property.
     * 
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property propertys is not an instance of
     * @param string $name The name of the property to get.
     * @return mixed The property value.
     *
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a write-only property.
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
     * Sets a property.
     * This method is called when an property is to be set.
     * 
     * @param string $name The name of the property to set.
     * @param mixed $value The property value.
     * @return void
     * @ignore
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the given property does not exist.
     * @throws ezcBaseValueException
     *         if the value to be assigned to a property is invalid.
     * @throws ezcBasePropertyPermissionException
     *         if the property to be set is a read-only property.
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'options':
                if ( ! $value instanceof ezcWebdavFileBackendOptions )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcWebdavFileBackendOptions' );
                }

                $this->$name = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Wait and get lock for complete directory tree.
     *
     * Acquire lock for the complete tree for read or write operations. This
     * does not implement any priorities for operations, or check if several
     * read operation may run in parallel. The plain locking should / could be
     * extended by something more sophisticated.
     *
     * If the tree already has been locked, the method waits until the lock can
     * be acquired.
     *
     * The optional second parameter $readOnly indicates wheather a read only
     * lock should be acquired. This may be used by extended implementations,
     * but it is not used in this implementation.
     *
     * @param bool $readOnly
     * @return void
     *
     * @todo The locking mechanism affects the ETag of the base collection. The
     *       ETag is different on each request, which might result in problems
     *       for clients that make extensive use of If-* headers. No client is
     *       known so far, if problems occur here we need to find a solution
     *       for this.
     */
    protected function acquireLock( $readOnly = false )
    {
        if ( $this->options->noLock )
        {
            return true;
        }
        
        try
        {
            $this->lock( $this->options->waitForLock, $this->options->lockTimeout );
        }
        catch ( ezcWebdavLockTimeoutException $e )
        {
            return false;
        }
        return true;
    }

    /**
     * Free lock.
     *
     * Frees the lock after the operation has been finished.
     * 
     * @return void
     */
    protected function freeLock()
    {
        if ( $this->options->noLock )
        {
            return true;
        }
        
        $this->unlock();
    }

    /**
     * Returns the mime type of a resource.
     *
     * Return the mime type of the resource identified by $path. If a mime type
     * extension is available it will be used to read the real mime type,
     * otherwise the original mime type passed by the client when uploading the
     * file will be returned. If no mimetype has ever been associated with the
     * file, the method will just return 'application/octet-stream'.
     * 
     * @param string $path 
     * @return string
     */
    protected function getMimeType( $path )
    {
        // Check if extension pecl/fileinfo is usable.
        if ( $this->options->useMimeExts && ezcBaseFeatures::hasExtensionSupport( 'fileinfo' ) )
        {
            $fInfo = new fInfo( FILEINFO_MIME );
            $mimeType = $fInfo->file( $this->root . $path );

            // The documentation tells to do this, but it does not work with a
            // current version of pecl/fileinfo
            // $fInfo->close();

            return $mimeType;
        }

        // Check if extension ext/mime-magic is usable.
        if ( $this->options->useMimeExts && 
             ezcBaseFeatures::hasExtensionSupport( 'mime_magic' ) &&
             ( $mimeType = mime_content_type( $this->root . $path ) ) !== false )
        {
            return $mimeType;
        }

        // Check if some browser submitted mime type is available.
        $storage = $this->getPropertyStorage( $path );
        $properties = $storage->getAllProperties();

        if ( isset( $properties['DAV:']['getcontenttype'] ) )
        {
            return $properties['DAV:']['getcontenttype']->mime;
        }

        // Default to 'application/octet-stream' if nothing else is available.
        return 'application/octet-stream';
    }

    /**
     * Creates a new collection.
     *
     * Creates a new collection at the given $path.
     * 
     * @param string $path 
     * @return void
     */
    protected function createCollection( $path )
    {
        mkdir( $this->root . $path );
        chmod( $this->root . $path, $this->options->directoryMode );

        // This automatically creates the property storage
        $storage = $this->getPropertyStoragePath( $path . '/foo' );
    }

    /**
     * Creates a new resource.
     *
     * Creates a new resource at the given $path, optionally with the given
     * content. If $content is empty, an empty resource will be created.
     * 
     * @param string $path 
     * @param string $content 
     * @return void
     */
    protected function createResource( $path, $content = null )
    {
        file_put_contents( $this->root . $path, $content );
        chmod( $this->root . $path, $this->options->fileMode );

        // This automatically creates the property storage if missing
        $storage = $this->getPropertyStoragePath( $path );
    }

    /**
     * Sets the contents of a resource.
     *
     * This method replaces the content of the resource identified by $path
     * with the submitted $content.
     * 
     * @param string $path 
     * @param string $content 
     * @return void
     */
    protected function setResourceContents( $path, $content )
    {
        file_put_contents( $this->root . $path, $content );
        chmod( $this->root . $path, $this->options->fileMode );
    }

    /**
     * Returns the contents of a resource.
     * 
     * This method returns the content of the resource identified by $path as a
     * string.
     *
     * @param string $path 
     * @return string
     */
    protected function getResourceContents( $path )
    {
        return file_get_contents( $this->root . $path );
    }

    /**
     * Returns the storage path for a property.
     *
     * Returns the file systems path where properties are stored for the
     * resource identified by $path. This depends on the name of the resource.
     * 
     * @param string $path 
     * @return string
     */
    protected function getPropertyStoragePath( $path )
    {
        // Get storage path for properties depending on the type of the
        // resource.
        $storagePath = realpath( $this->root . dirname( $path ) ) 
            . '/' . $this->options->propertyStoragePath . '/'
            . basename( $path ) . '.xml';

        // Create property storage if it does not exist yet
        if ( !is_dir( dirname( $storagePath ) ) )
        {
            mkdir( dirname( $storagePath ), $this->options->directoryMode );
        }

        // Append name of namespace to property storage path
        return $storagePath;
    }

    /**
     * Returns the property storage for a resource.
     *
     * Returns the {@link ezcWebdavPropertyStorage} instance containing the
     * properties for the resource identified by $path.
     * 
     * @param string $path 
     * @return ezcWebdavBasicPropertyStorage
     */
    protected function getPropertyStorage( $path )
    {
        $storagePath = $this->getPropertyStoragePath( $path );

        // If no properties has been stored yet, just return an empty property
        // storage.
        if ( !is_file( $storagePath ) )
        {
            return new ezcWebdavBasicPropertyStorage();
        }

        // Create handler structure to read properties
        $handler = new ezcWebdavPropertyHandler(
            $xml = new ezcWebdavXmlTool()
        );
        $storage = new ezcWebdavBasicPropertyStorage();

        // Read document
        try
        {
             $doc = $xml->createDom( file_get_contents( $storagePath ) );
        }
        catch ( ezcWebdavInvalidXmlException $e )
        {
            throw new ezcWebdavFileBackendBrokenStorageException(
                "Could not open XML as DOMDocument: '{$storage}'."
            );
        }

        // Get property node from document
        $properties = $doc->getElementsByTagname( 'properties' )->item( 0 )->childNodes;

        // Extract and return properties
        $handler->extractProperties( 
            $properties,
            $storage
        );

        return $storage;
    }

    /**
     * Stores properties for a resource.
     *
     * Creates a new property storage file and stores the properties given for
     * the resource identified by $path.  This depends on the affected resource
     * and the actual properties in the property storage.
     * 
     * @param string $path 
     * @param ezcWebdavBasicPropertyStorage $storage 
     * @return void
     */
    protected function storeProperties( $path, ezcWebdavBasicPropertyStorage $storage )
    {
        $storagePath = $this->getPropertyStoragePath( $path );

        // Create handler structure to read properties
        $handler = new ezcWebdavPropertyHandler(
            $xml = new ezcWebdavXmlTool()
        );

        // Create new dom document with property storage for one namespace
        $doc = new DOMDocument( '1.0' );

        $properties = $doc->createElement( 'properties' );
        $doc->appendChild( $properties );

        // Store and store properties
        $handler->serializeProperties(
            $storage,
            $properties
        );

        return $doc->save( $storagePath );
    }

    /**
     * Manually sets a property on a resource.
     *
     * Sets the given $propertyBackup for the resource identified by $path.
     * 
     * @param string $path 
     * @param ezcWebdavProperty $property
     * @return bool
     */
    public function setProperty( $path, ezcWebdavProperty $property )
    {
        // Check if property is a self handled live property and return an
        // error in this case.
        if ( ( $property->namespace === 'DAV:' ) &&
             in_array( $property->name, $this->handledLiveProperties, true ) &&
             ( $property->name !== 'getcontenttype' ) &&
             ( $property->name !== 'lockdiscovery' ) )
        {
            return false;
        }

        // Get namespace property storage
        $storage = $this->getPropertyStorage( $path );

        // Attach property to store
        $storage->attach( $property );

        // Store document back
        $this->storeProperties( $path, $storage );

        return true;
    }

    /**
     * Manually removes a property from a resource.
     *
     * Removes the given $property form the resource identified by $path.
     * 
     * @param string $path 
     * @param ezcWebdavProperty $property
     * @return bool
     */
    public function removeProperty( $path, ezcWebdavProperty $property )
    {
        // Live properties may not be removed.
        if ( $property instanceof ezcWebdavLiveProperty )
        {
            return false;
        }

        // Get namespace property storage
        $storage = $this->getPropertyStorage( $path );

        // Attach property to store
        $storage->detach( $property->name, $property->namespace );

        // Store document back
        $this->storeProperties( $path, $storage );

        return true;
    }

    /**
     * Resets the property storage for a resource.
     *
     * Discardes the current {@link ezcWebdavPropertyStorage} of the resource
     * identified by $path and replaces it with the given $properties.
     * 
     * @param string $path 
     * @param ezcWebdavPropertyStorage $storage
     * @return bool
     */
    public function resetProperties( $path, ezcWebdavPropertyStorage $storage )
    {
        $this->storeProperties( $path, $storage );
    }

    /**
     * Returns a property of a resource.
     * 
     * Returns the property with the given $propertyName, from the resource
     * identified by $path. You may optionally define a $namespace to receive
     * the property from.
     *
     * @param string $path 
     * @param string $propertyName 
     * @param string $namespace 
     * @return ezcWebdavProperty
     */
    public function getProperty( $path, $propertyName, $namespace = 'DAV:' )
    {
        $storage = $this->getPropertyStorage( $path );

        // Handle dead propreties
        if ( $namespace !== 'DAV:' )
        {
            $properties = $storage->getAllProperties();
            return $properties[$namespace][$propertyName];
        }

        // Handle live properties
        switch ( $propertyName )
        {
            case 'getcontentlength':
                $property = new ezcWebdavGetContentLengthProperty();
                $property->length = $this->getContentLength( $path );
                return $property;

            case 'getlastmodified':
                $property = new ezcWebdavGetLastModifiedProperty();
                $property->date = new ezcWebdavDateTime( '@' . filemtime( $this->root . $path ) );
                return $property;

            case 'creationdate':
                $property = new ezcWebdavCreationDateProperty();
                $property->date = new ezcWebdavDateTime( '@' . filectime( $this->root . $path ) );
                return $property;

            case 'displayname':
                $property = new ezcWebdavDisplayNameProperty();
                $property->displayName = urldecode( basename( $path ) );
                return $property;

            case 'getcontenttype':
                $property = new ezcWebdavGetContentTypeProperty(
                    $this->getMimeType( $path )
                );
                return $property;

            case 'getetag':
                $property = new ezcWebdavGetEtagProperty();
                $property->etag = $this->getETag( $path );
                return $property;

            case 'resourcetype':
                $property = new ezcWebdavResourceTypeProperty();
                $property->type = $this->isCollection( $path ) ?
                    ezcWebdavResourceTypeProperty::TYPE_COLLECTION : 
                    ezcWebdavResourceTypeProperty::TYPE_RESOURCE;
                return $property;

            case 'supportedlock':
                $property = new ezcWebdavSupportedLockProperty();
                return $property;

            case 'lockdiscovery':
                $property = new ezcWebdavLockDiscoveryProperty();
                return $property;

            default:
                // Handle all other live properties like dead properties
                $properties = $storage->getAllProperties();
                return $properties[$namespace][$propertyName];
        }
    }

    /**
     * Returns the content length.
     *
     * Returns the content length (filesize) of the resource identified by
     * $path. 
     *
     * @param string $path
     * @return string The content length.
     */
    private function getContentLength( $path )
    {
        $length = ezcWebdavGetContentLengthProperty::COLLECTION;
        if ( !$this->isCollection( $path ) )
        {
            $length = (string) filesize( $this->root . $path );
        }
        return $length;
    }

    /**
     * Returns the etag representing the current state of $path.
     * 
     * Calculates and returns the ETag for the resource represented by $path.
     * The ETag is calculated from the $path itself and the following
     * properties, which are concatenated and md5 hashed:
     *
     * <ul>
     *  <li>getcontentlength</li>
     *  <li>getlastmodified</li>
     * </ul>
     *
     * This method can be overwritten in custom backend implementations to
     * access the information needed directly without using the way around
     * properties.
     *
     * Custom backend implementations are encouraged to use the same mechanism
     * (or this method itself) to determine and generate ETags.
     * 
     * @param mixed $path 
     * @return void
     */
    protected function getETag( $path )
    {
        clearstatcache();
        return md5(
            $path
            . $this->getContentLength( $path )
            . date( 'c', filemtime( $this->root . $path ) )
        );
    }

    /**
     * Returns all properties for a resource.
     * 
     * Returns all properties for the resource identified by $path as a {@link
     * ezcWebdavBasicPropertyStorage}.
     *
     * @param string $path 
     * @return ezcWebdavPropertyStorage
     */
    public function getAllProperties( $path )
    {
        $storage = $this->getPropertyStorage( $path );
        
        // Add all live properties to stored properties
        foreach ( $this->handledLiveProperties as $property )
        {
            $storage->attach(
                $this->getProperty( $path, $property )
            );
        }

        return $storage;
    }

    /**
     * Recursively copy a file or directory.
     *
     * Recursively copy a file or directory in $source to the given
     * $destination. If a $depth is given, the operation will stop as soon as
     * the given recursion depth is reached. A depth of -1 means no limit,
     * while a depth of 0 means, that only the current file or directory will
     * be copied, without any recursion.
     *
     * Returns an empty array if no errors occured, and an array with the files
     * which caused errors otherwise.
     * 
     * @param string $source 
     * @param string $destination 
     * @param int $depth 
     * @return array
     */
    public function copyRecursive( $source, $destination, $depth = ezcWebdavRequest::DEPTH_INFINITY )
    {
        // Skip non readable files in source directory, or non writeable
        // destination directories.
        if ( !is_readable( $source ) || !is_writeable( dirname( $destination ) ) )
        {
            return array( $source );
        }

        // Copy
        if ( is_dir( $source ) )
        {
            mkdir( $destination );
            // To ignore umask, umask() should not be changed on multithreaded
            // servers...
            chmod( $destination, $this->options->directoryMode );
        } 
        elseif ( is_file( $source ) )
        {
            copy( $source, $destination );
            chmod( $destination, $this->options->fileMode );
        }

        if ( ( $depth === ezcWebdavRequest::DEPTH_ZERO ) ||
             ( !is_dir( $source ) ) )
        {
            // Do not recurse (any more)
            return array();
        }

        // Recurse
        $dh = opendir( $source );
        $errors = array();
        while ( $file = readdir( $dh ) )
        {
            if ( ( $file === '.' ) ||
                 ( $file === '..' ) )
            {
                continue;
            }

            $errors = array_merge(
                $errors,
                $this->copyRecursive( 
                    $source . '/' . $file, 
                    $destination . '/' . $file,
                    $depth - 1
                )
            );
        }
        closedir( $dh );

        return $errors;
    }

    /**
     * Copies resources recursively from one path to another.
     *
     * Copies the resourced identified by $fromPath recursively to $toPath with
     * the given $depth, where $depth is one of {@link
     * ezcWebdavRequest::DEPTH_ZERO}, {@link ezcWebdavRequest::DEPTH_ONE},
     * {@link ezcWebdavRequest::DEPTH_INFINITY}.
     *
     * Returns an array with {@link ezcWebdavErrorResponse}s for all subtrees,
     * where the copy operation failed. Errors for subsequent resources in a
     * subtree should be ommitted.
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
        $errors = $this->copyRecursive( $this->root . $fromPath, $this->root . $toPath, $depth );

        // Transform errors
        foreach ( $errors as $nr => $error )
        {
            $errors[$nr] = new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_423,
                str_replace( $this->root, '', $error )
            );
        }

        // Copy dead properties
        $storage = $this->getPropertyStorage( $fromPath );
        $this->storeProperties( $toPath, $storage );

        // Updateable live properties are updated automagically, because they
        // are regenerated on request on base of the file they affect. So there
        // is no reason to keep them "alive".

        return $errors;
    }

    /**
     * Returns if everything below a path can be deleted recursively.
     *
     * Checks files and directories recursively and returns if everything can
     * be deleted.  Returns an empty array if no errors occured, and an array
     * with the files which caused errors otherwise.
     *
     * @param string $source 
     * @return array
     */
    public function checkDeleteRecursive( $source )
    {
        // Skip non readable files in source directory, or non writeable
        // destination directories.
        if ( !is_writeable( dirname( $source ) ) )
        {
            return array(
                new ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_403,
                    substr( $source, strlen( $this->root ) )
                ),
            );
        }

        if ( is_file( $source ) )
        {
            // For plain files the above checks should be sufficant
            return array();
        }

        // Recurse
        $dh = opendir( $source );
        $errors = array();
        while ( $file = readdir( $dh ) )
        {
            if ( ( $file === '.' ) ||
                 ( $file === '..' ) )
            {
                continue;
            }

            $errors = array_merge(
                $errors,
                $this->checkDeleteRecursive( $source . '/' . $file )
            );
        }
        closedir( $dh );

        // Return errors
        return $errors;
    }

    /**
     * Deletes everything below a path.
     *
     * Deletes the resource identified by $path recursively. Returns an
     * instance of {@link ezcWebdavErrorResponse} if the deletion failed, and
     * null on success.
     * 
     * @param string $path 
     * @return ezcWebdavErrorResponse
     */
    protected function performDelete( $path )
    {
        $errors = $this->checkDeleteRecursive( $this->root . $path );

        // If an error will occur return the proper status. We return
        // multistatus in any case.
        if ( count( $errors ) )
        {
            return new ezcWebdavMultistatusResponse(
                $errors
            );
        }

        // Just delete otherwise
        if ( is_file( $this->root . $path ) )
        {
            unlink( $this->root . $path );
        }
        else
        {
            ezcBaseFile::removeRecursive( $this->root . $path );
        }

        // Finally empty property storage for removed node
        $storagePath = $this->getPropertyStoragePath( $path );
        if ( is_file( $storagePath ) )
        {
            unlink( $storagePath );
        }

        return null;
    }

    /**
     * Returns if a resource exists.
     *
     * Returns if a the resource identified by $path exists.
     * 
     * @param string $path 
     * @return bool
     */
    protected function nodeExists( $path )
    {
        return ( is_file( $this->root . $path ) || is_dir( $this->root . $path ) );
    }

    /**
     * Returns if resource is a collection.
     *
     * Returns if the resource identified by $path is a collection resource
     * (true) or a non-collection one (false).
     * 
     * @param string $path 
     * @return bool
     */
    protected function isCollection( $path )
    {
        return is_dir( $this->root . $path );
    }

    /**
     * Returns members of collection.
     *
     * Returns an array with the members of the collection identified by $path.
     * The returned array can contain {@link ezcWebdavCollection}, and {@link
     * ezcWebdavResource} instances and might also be empty, if the collection
     * has no members.
     * 
     * @param string $path 
     * @return array(ezcWebdavResource|ezcWebdavCollection)
     */
    protected function getCollectionMembers( $path )
    {
        $contents = array();
        $errors = array();

        $files = glob( $this->root . $path . '/*' );

        if ( $this->options->hideDotFiles === false )
        {
            $files = array_merge(
                $files,
                glob( $this->root . $path . '/.*' )
            );
        }

        foreach ( $files as $file )
        {
            // Skip files used for somethig else...
            if ( ( strpos( $file, '/' . $this->options->lockFileName ) !== false ) ||
                 ( strpos( $file, '/' . $this->options->propertyStoragePath ) !== false ) )
            {
                continue;
            }

            $file = $path . '/' . basename( $file );
            if ( is_dir( $this->root . $file ) )
            {
                // Add collection without any children
                $contents[] = new ezcWebdavCollection( $file );
            }
            else
            {
                // Add files without content
                $contents[] = new ezcWebdavResource( $file );
            }
        }

        return $contents;
    }

    /**
     * Serves GET requests.
     *
     * The method receives a {@link ezcWebdavGetRequest} object containing all
     * relevant information obout the clients request and will return an {@link
     * ezcWebdavErrorResponse} instance on error or an instance of {@link
     * ezcWebdavGetResourceResponse} or {@link ezcWebdavGetCollectionResponse}
     * on success, depending on the type of resource that is referenced by the
     * request.
     *
     * This method acquires the internal lock of the backend, dispatches to
     * {@link ezcWebdavSimpleBackend} to perform the operation and releases the
     * lock afterwards.
     *
     * @param ezcWebdavGetRequest $request
     * @return ezcWebdavResponse
     */
    public function get( ezcWebdavGetRequest $request )
    {
        $this->acquireLock( true );
        $return = parent::get( $request );
        $this->freeLock();

        return $return;
    }

    /**
     * Serves HEAD requests.
     *
     * The method receives a {@link ezcWebdavHeadRequest} object containing all
     * relevant information obout the clients request and will return an {@link
     * ezcWebdavErrorResponse} instance on error or an instance of {@link
     * ezcWebdavHeadResponse} on success.
     *
     * This method acquires the internal lock of the backend, dispatches to
     * {@link ezcWebdavSimpleBackend} to perform the operation and releases the
     * lock afterwards.
     * 
     * @param ezcWebdavHeadRequest $request
     * @return ezcWebdavResponse
     */
    public function head( ezcWebdavHeadRequest $request )
    {
        $this->acquireLock( true );
        $return = parent::head( $request );
        $this->freeLock();

        return $return;
    }

    /**
     * Serves PROPFIND requests.
     * 
     * The method receives a {@link ezcWebdavPropFindRequest} object containing
     * all relevant information obout the clients request and will either
     * return an instance of {@link ezcWebdavErrorResponse} to indicate an error
     * or a {@link ezcWebdavPropFindResponse} on success. If the referenced
     * resource is a collection or if some properties produced errors, an
     * instance of {@link ezcWebdavMultistatusResponse} may be returned.
     *
     * The {@link ezcWebdavPropFindRequest} object contains a definition to
     * find one or more properties of a given collection or non-collection
     * resource.
     *
     * This method acquires the internal lock of the backend, dispatches to
     * {@link ezcWebdavSimpleBackend} to perform the operation and releases the
     * lock afterwards.
     *
     * @param ezcWebdavPropFindRequest $request
     * @return ezcWebdavResponse
     */
    public function propFind( ezcWebdavPropFindRequest $request )
    {
        $this->acquireLock( true );
        $return = parent::propFind( $request );
        $this->freeLock();

        return $return;
    }

    /**
     * Serves PROPPATCH requests.
     * 
     * The method receives a {@link ezcWebdavPropPatchRequest} object
     * containing all relevant information obout the clients request and will
     * return an instance of {@link ezcWebdavErrorResponse} on error or a
     * {@link ezcWebdavPropPatchResponse} response on success. If the
     * referenced resource is a collection or if only some properties produced
     * errors, an instance of {@link ezcWebdavMultistatusResponse} may be
     * returned.
     *
     * This method acquires the internal lock of the backend, dispatches to
     * {@link ezcWebdavSimpleBackend} to perform the operation and releases the
     * lock afterwards.
     *
     * @param ezcWebdavPropPatchRequest $request
     * @return ezcWebdavResponse
     */
    public function propPatch( ezcWebdavPropPatchRequest $request )
    {
        $this->acquireLock();
        $return = parent::propPatch( $request );
        $this->freeLock();

        return $return;
    }

    /**
     * Serves PUT requests.
     *
     * The method receives a {@link ezcWebdavPutRequest} objects containing all
     * relevant information obout the clients request and will return an
     * instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavPutResponse} on success.
     * 
     * This method acquires the internal lock of the backend, dispatches to
     * {@link ezcWebdavSimpleBackend} to perform the operation and releases the
     * lock afterwards.
     *
     * @param ezcWebdavPutRequest $request 
     * @return ezcWebdavResponse
     */
    public function put( ezcWebdavPutRequest $request )
    {
        $this->acquireLock();
        $return = parent::put( $request );
        $this->freeLock();

        return $return;
    }

    /**
     * Serves DELETE requests.
     *
     * The method receives a {@link ezcWebdavDeleteRequest} objects containing
     * all relevant information obout the clients request and will return an
     * instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavDeleteResponse} on success.
     *
     * This method acquires the internal lock of the backend, dispatches to
     * {@link ezcWebdavSimpleBackend} to perform the operation and releases the
     * lock afterwards.
     * 
     * @param ezcWebdavDeleteRequest $request 
     * @return ezcWebdavResponse
     */
    public function delete( ezcWebdavDeleteRequest $request )
    {
        $this->acquireLock();
        $return = parent::delete( $request );
        $this->freeLock();

        return $return;
    }

    /**
     * Serves COPY requests.
     *
     * The method receives a {@link ezcWebdavCopyRequest} objects containing
     * all relevant information obout the clients request and will return an
     * instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavCopyResponse} on success. If only some operations failed, this
     * method may return an instance of {@link ezcWebdavMultistatusResponse}.
     *
     * This method acquires the internal lock of the backend, dispatches to
     * {@link ezcWebdavSimpleBackend} to perform the operation and releases the
     * lock afterwards.
     * 
     * @param ezcWebdavCopyRequest $request 
     * @return ezcWebdavResponse
     */
    public function copy( ezcWebdavCopyRequest $request )
    {
        $this->acquireLock();
        $return = parent::copy( $request );
        $this->freeLock();

        return $return;
    }

    /**
     * Serves MOVE requests.
     *
     * The method receives a {@link ezcWebdavMoveRequest} objects containing
     * all relevant information obout the clients request and will return an
     * instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavMoveResponse} on success. If only some operations failed, this
     * method may return an instance of {@link ezcWebdavMultistatusResponse}.
     *
     * This method acquires the internal lock of the backend, dispatches to
     * {@link ezcWebdavSimpleBackend} to perform the operation and releases the
     * lock afterwards.
     * 
     * @param ezcWebdavMoveRequest $request 
     * @return ezcWebdavResponse
     */
    public function move( ezcWebdavMoveRequest $request )
    {
        $this->acquireLock();
        $return = parent::move( $request );
        $this->freeLock();

        return $return;
    }

    /**
     * Serves MKCOL (make collection) requests.
     *
     * The method receives a {@link ezcWebdavMakeCollectionRequest} objects
     * containing all relevant information obout the clients request and will
     * return an instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavMakeCollectionResponse} on success.
     *
     * This method acquires the internal lock of the backend, dispatches to
     * {@link ezcWebdavSimpleBackend} to perform the operation and releases the
     * lock afterwards.
     * 
     * @param ezcWebdavMakeCollectionRequest $request 
     * @return ezcWebdavResponse
     */
    public function makeCollection( ezcWebdavMakeCollectionRequest $request )
    {
        $this->acquireLock();
        $return = parent::makeCollection( $request );
        $this->freeLock();

        return $return;
    }
}

?>
