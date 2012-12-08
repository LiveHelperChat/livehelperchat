<?php
/**
 * File containing the abstract ezcWebdavSimpleBackend class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Abstract base class for common backend operations.
 *
 * This base backend provides the generic handling of requests and dispatches the
 * required actions to some basic manipulation methods, which you are required
 * to implement, when extending this base class.
 *
 * This backend does not provide support for extended Webdav features, like
 * compression, or lock handling by the backend, therefore the {@link
 * getFeatures()} method is final. If you want to develop a backend which is
 * capable of manual handling those features directly extend from {@link
 * ezcWebdavBackend}.
 *
 * @version 1.1.4
 * @package Webdav
 * @mainclass
 */
abstract class ezcWebdavSimpleBackend extends ezcWebdavBackend implements ezcWebdavBackendPut, ezcWebdavBackendChange, ezcWebdavBackendMakeCollection
{
    /**
     * Create a new collection.
     *
     * Creates a new collection at the given $path.
     * 
     * @param string $path 
     * @return void
     */
    abstract protected function createCollection( $path );

    /**
     * Create a new resource.
     *
     * Creates a new resource at the given $path, optionally with the given
     * $content.
     * 
     * @param string $path 
     * @param string $content 
     * @return void
     */
    abstract protected function createResource( $path, $content = null );

    /**
     * Changes contents of a resource.
     *
     * This method is used to change the contents of the resource identified by
     * $path to the given $content.
     * 
     * @param string $path 
     * @param string $content 
     * @return void
     */
    abstract protected function setResourceContents( $path, $content );

    /**
     * Returns the content of a resource.
     *
     * Returns the content of the resource identified by $path.
     * 
     * @param string $path 
     * @return string
     */
    abstract protected function getResourceContents( $path );

    /**
     * Manually sets a property on a resource.
     *
     * Sets the given $propertyBackup for the resource identified by $path.
     * 
     * @param string $path 
     * @param ezcWebdavProperty $property
     * @return bool
     */
    abstract public function setProperty( $path, ezcWebdavProperty $property );

    /**
     * Manually removes a property from a resource.
     *
     * Removes the given $property form the resource identified by $path.
     * 
     * @param string $path 
     * @param ezcWebdavProperty $property
     * @return bool
     */
    abstract public function removeProperty( $path, ezcWebdavProperty $property );

    /**
     * Resets the property storage for a resource.
     *
     * Discardes the current {@link ezcWebdavPropertyStorage} of the resource
     * identified by $path and replaces it with the given $properties.
     * 
     * @param string $path 
     * @param ezcWebdavPropertyStorage $properties
     * @return bool
     */
    abstract public function resetProperties( $path, ezcWebdavPropertyStorage $properties );

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
    abstract public function getProperty( $path, $propertyName, $namespace = 'DAV:' );

    /**
     * Returns all properties for a resource.
     * 
     * Returns all properties for the resource identified by $path as a {@link
     * ezcWebdavBasicPropertyStorage}.
     *
     * @param string $path 
     * @return ezcWebdavPropertyStorage
     */
    abstract public function getAllProperties( $path );

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
    abstract protected function performCopy( $fromPath, $toPath, $depth = ezcWebdavRequest::DEPTH_INFINITY );

    /**
     * Deletes everything below a path.
     *
     * Deletes the resource identified by $path recursively. Returns an
     * instance of {@link ezcWebdavMultistatusResponse} if the deletion failed,
     * and null on success.
     * 
     * @param string $path 
     * @return ezcWebdavMultitstatusResponse|null
     */
    abstract protected function performDelete( $path );

    /**
     * Returns if a resource exists.
     *
     * Returns if a the resource identified by $path exists.
     * 
     * @param string $path 
     * @return bool
     */
    abstract protected function nodeExists( $path );

    /**
     * Returns if resource is a collection.
     *
     * Returns if the resource identified by $path is a collection resource
     * (true) or a non-collection one (false).
     * 
     * @param string $path 
     * @return bool
     */
    abstract protected function isCollection( $path );

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
    abstract protected function getCollectionMembers( $path );

    /**
     * Returns additional features supported by the backend.
     *
     * Returns a bitmap of additional features supported by the backend, referenced
     * by constants from the basic {@link ezcWebdavBackend} class.
     * 
     * @return int
     */
    public final function getFeatures()
    {
        return 0;
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
     * @param ezcWebdavGetRequest $request
     * @return ezcWebdavResponse
     */
    public function get( ezcWebdavGetRequest $request )
    {
        $source = $request->requestUri;

        // Check authorization
        if ( !ezcWebdavServer::getInstance()->isAuthorized( $source, $request->getHeader( 'Authorization' ) ) )
        {
            return $this->createUnauthorizedResponse(
                $source,
                $request->getHeader( 'Authorization' )
            );
        }

        // Check if resource is available
        if ( !$this->nodeExists( $source ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_404,
                $source
            );
        }

        // Verify If-[None-]Match headers
        if ( ( $res = $this->checkIfMatchHeaders( $request, $source ) ) !== null )
        {
            return $res;
        }
        
        $res = null; // Init
        if ( !$this->isCollection( $source ) )
        {
            // Just deliver file
            $res = new ezcWebdavGetResourceResponse(
                new ezcWebdavResource(
                    $source,
                    $this->getAllProperties( $source ),
                    $this->getResourceContents( $source )
                )
            );
        }
        else
        {
            // Return collection with contained children
            $res = new ezcWebdavGetCollectionResponse(
                new ezcWebdavCollection(
                    $source,
                    $this->getAllProperties( $source ),
                    $this->getCollectionMembers( $source )
                )
            );
        }
        
        // Add ETag header
        $res->setHeader( 'ETag', $this->getETag( $source ) );

        // Deliver response
        return $res;
    }

    /**
     * Serves HEAD requests.
     *
     * The method receives a {@link ezcWebdavHeadRequest} object containing all
     * relevant information obout the clients request and will return an {@link
     * ezcWebdavErrorResponse} instance on error or an instance of {@link
     * ezcWebdavHeadResponse} on success.
     * 
     * @param ezcWebdavHeadRequest $request
     * @return ezcWebdavResponse
     */
    public function head( ezcWebdavHeadRequest $request )
    {
        $source = $request->requestUri;

        // Check authorization
        if ( !ezcWebdavServer::getInstance()->isAuthorized( $source, $request->getHeader( 'Authorization' ) ) )
        {
            return $this->createUnauthorizedResponse(
                $source,
                $request->getHeader( 'Authorization' )
            );
        }

        // Check if resource is available
        if ( !$this->nodeExists( $source ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_404,
                $source
            );
        }
        
        $res = null; // Init
        if ( !$this->isCollection( $source ) )
        {
            // Just deliver file without contents
            $res = new ezcWebdavHeadResponse(
                new ezcWebdavResource(
                    $source,
                    $this->getAllProperties( $source )
                )
            );
        }
        else
        {
            // Just deliver collection without children
            $res = new ezcWebdavHeadResponse(
                new ezcWebdavCollection(
                    $source,
                    $this->getAllProperties( $source )
                )
            );
        }

        // Add ETag header
        $res->setHeader( 'ETag', $this->getETag( $source ) );

        // Deliver response
        return $res;
    }

    /**
     * Returns all child nodes.
     *
     * Get all nodes from the resource identified by $source up to the given
     * depth. Reuses the method {@link getCollectionMembers()}, but you may
     * want to overwrite this implementation by somethings which fits better
     * with your backend.
     * 
     * @param string $source 
     * @param int $depth 
     * @return array(ezcWebdavResource|ezcWebdavCollection)
     */
    protected function getNodes( $source, $depth )
    {
        // No special handling for plain resources
        if ( !$this->isCollection( $source ) )
        {
            return array( new ezcWebdavResource( $source ) );
        }

        // For zero depth just return the collection
        if ( $depth === ezcWebdavRequest::DEPTH_ZERO )
        {
            return array( new ezcWebdavCollection( $source ) );
        }

        $nodes = array( new ezcWebdavCollection( $source ) );
        $recurseCollections = array( $source );

        // Collect children for all collections listed in $recurseCollections.
        for ( $i = 0; $i < count( $recurseCollections ); ++$i )
        {
            $source = $recurseCollections[$i];
            $children = $this->getCollectionMembers( $source );

            foreach ( $children as $child )
            {
                $nodes[] = $child;

                // Check if we should recurse deeper, and add collections to
                // processing list in this case.
                if ( ( $child instanceof ezcWebdavCollection ) && 
                     ( $depth === ezcWebdavRequest::DEPTH_INFINITY ) )
                {
                    $recurseCollections[] = $child->path;
                }
            }
        }

        return $nodes;
    }

    /**
     * Returns properties, fetched by name.
     *
     * Fetch properties as defined by the passed $request for the resource
     * referenced. Properties are fetched by their names.
     *
     * This method checks also for each of the nodes affected by the request if
     * authorization suceeds.
     * 
     * @param ezcWebdavPropFindRequest $request 
     * @return ezcWebdavResponse
     */
    protected function fetchProperties( ezcWebdavPropFindRequest $request )
    {
        $source = $request->requestUri;

        // Get list of all affected node, depeding on source and depth
        $nodes = $this->getNodes( $source, $request->getHeader( 'Depth' ) );

        // Pathes which were already determined as unauthorized
        $unauthorizedPaths = array();

        $server = ezcWebdavServer::getInstance();
        $performAuth = ( $server->auth !== null && $server->auth instanceof ezcWebdavAuthorizer );

        // Get requested properties for all files
        $responses = array();

        foreach ( $nodes as $node )
        {
            // Responses for the current node
            $nodeResponses = array();

            // Authorization
            $authorized = true;
            if ( $performAuth )
            {
                $nodePath = $node->path;

                foreach ( $unauthorizedPaths as $unauthorizedPath )
                {
                    // Check if a parent path was already determined as unauthorized
                    if ( strpos( $nodePath, $unauthorizedPath ) === 0 )
                    {
                        // Skip this node completely, since we already have a
                        // parent node with 403
                        continue 2;
                    }
                }

                // Check authorization
                if ( !ezcWebdavServer::getInstance()->isAuthorized( $nodePath, $request->getHeader( 'Authorization' ) ) )
                {
                    $authorized          = false;
                    $unauthorizedPaths[] = $nodePath;
                }
            }

            if ( !$authorized )
            {
                $nodeResponses[] = new ezcWebdavPropStatResponse(
                    $request->prop,
                    // We send 403 Forbidden here. Hope that's correct? RFC
                    // does not state anything...
                    ezcWebdavResponse::STATUS_403
                );
            }
            else
            {
                // Get all properties form node ...
                $nodeProperties = $this->getAllProperties( $node->path );
            
                // ... and diff the with the requested properties.
                $notFound = $request->prop->diff( $nodeProperties );
                $valid = $nodeProperties->intersect( $request->prop );
                
                // Add propstat sub response for valid responses
                if ( count( $valid ) )
                {
                    $nodeResponses[] = new ezcWebdavPropStatResponse( $valid );
                }

                // Only create error response, when some properties could not be
                // found.
                if ( count( $notFound ) )
                {
                    $nodeResponses[] = new ezcWebdavPropStatResponse(
                        $notFound,
                        ezcWebdavResponse::STATUS_404
                    );
                }
            }

            // Create response
            $responses[] = new ezcWebdavPropFindResponse(
                $node,
                $nodeResponses
            );
        }

        return new ezcWebdavMultistatusResponse( $responses );
    }

    /**
     * Returns names of all available properties for a resource.
     *
     * Fetches the names of all properties assigned to the reosource referenced
     * in $request and, if the resozurce is a collection, also returns property
     * names for its children, depending on the depth header of the $request.
     * 
     * @param ezcWebdavPropFindRequest $request 
     * @return ezcWebdavResponse
     */
    protected function fetchPropertyNames( ezcWebdavPropFindRequest $request )
    {
        $source = $request->requestUri;

        // Get list of all affected node, depeding on source and depth
        $nodes = $this->getNodes( $source, $request->getHeader( 'Depth' ) );

        // Pathes which were already determined as unauthorized
        $unauthorizedPaths = array();

        $server = ezcWebdavServer::getInstance();
        $performAuth = ( $server->auth !== null && $server->auth instanceof ezcWebdavAuthorizer );

        // Get requested properties for all files
        $responses = array();
        foreach ( $nodes as $node )
        {
            if ( $performAuth )
            {
                $nodePath = $node->path;

                foreach ( $unauthorizedPaths as $unauthorizedPath )
                {
                    // Check if a parent path was already determined as unauthorized
                    if ( substr( $nodePath, $unauthorizedPath ) === 0 )
                    {
                        // Skip this node completely, since we already have a
                        // parent node with error response
                        continue 2;
                    }
                }

                // Check authorization
                if ( !ezcWebdavServer::getInstance()->isAuthorized( $nodePath, $request->getHeader( 'Authorization' ) ) )
                {
                    $unauthorizedPaths[] = $nodePath;
                    // Silently exclude unauthorized properties.
                    $responses[] = new ezcWebdavPropFindResponse(
                        $node,
                        new ezcWebdavPropStatResponse( new ezcWebdavBasicPropertyStorage() )
                    );
                    // Skip further processing of this node
                    continue;
                }
            }

            // Get all properties form node ...
            $nodeProperties = $this->getAllProperties( $node->path );

            // ... and clear and add them to the property name storage.
            $propertyNames = new ezcWebdavBasicPropertyStorage();
            foreach ( $nodeProperties->getAllProperties() as $namespace => $properties )
            {
                foreach ( $properties as $name => $property )
                {
                    // Clear property, because the client only want the names
                    // of the available properties.
                    $property = clone $property;
                    $property->clear();
                    $propertyNames->attach( $property );
                }
            }

            // Add response
            $responses[] = new ezcWebdavPropFindResponse(
                $node,
                new ezcWebdavPropStatResponse( $propertyNames )
            );
        }

        return new ezcWebdavMultistatusResponse( $responses );
    }

    /**
     * Returns all available properties for a resource.
     *
     * Fetches all available properties assigned to the reosource referenced in
     * $request and, if the resource is a collection, also returns properties
     * for its children, depending on the depth header of the $request. The
     * instances of {@link ezcWebdavPropFindResponse} generated by this method
     * are encapsulated in a {@link ezcWebdavMultistatusResponse} object.
     * 
     * @param ezcWebdavPropFindRequest $request 
     * @return ezcWebdavMultistatusResponse
     */
    protected function fetchAllProperties( ezcWebdavPropFindRequest $request )
    {
        $source = $request->requestUri;

        // Get list of all affected node, depeding on source and depth
        $nodes = $this->getNodes( $source, $request->getHeader( 'Depth' ) );
        
        // Pathes which were already determined as unauthorized
        $unauthorizedPaths = array();

        $server = ezcWebdavServer::getInstance();
        $performAuth = ( $server->auth !== null && $server->auth instanceof ezcWebdavAuthorizer );

        // Get requested properties for all files
        $responses = array();
        foreach ( $nodes as $node )
        {
            if ( $performAuth )
            {
                foreach ( $unauthorizedPaths as $unauthorizedPath )
                {
                    // Check if a parent path was already determined as unauthorized
                    if ( substr_compare( $node->path, $unauthorizedPath, 0, strlen( $unauthorizedPath ) ) === 0 )
                    {
                        // Skip this node completely, since we already have a
                        // parent node with 403
                        continue 2;
                    }
                }

                $nodePath = $node->path;

                // Check authorization
                if ( !ezcWebdavServer::getInstance()->isAuthorized( $nodePath, $request->getHeader( 'Authorization' ) ) )
                {
                    $responses[] = $this->createUnauthorizedResponse(
                        $nodePath,
                        $request->getHeader( 'Authorization' )
                    );
                    $unauthorizedPaths[] = $nodePath;
                    // Skip further processing of node
                    continue;
                }
            }

            // Just create response from properties
            $responses[] = new ezcWebdavPropFindResponse(
                $node,
                new ezcWebdavPropStatResponse( 
                    $this->getAllProperties( $node->path )
                )
            );
        }

        return new ezcWebdavMultistatusResponse( $responses );
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
     * @param ezcWebdavPropFindRequest $request
     * @return ezcWebdavResponse
     */
    public function propFind( ezcWebdavPropFindRequest $request )
    {
        $source = $request->requestUri;

        if ( !ezcWebdavServer::getInstance()->isAuthorized( $source, $request->getHeader( 'Authorization' ) ) )
        {
            // Globally issue a 401, if the user does not have access to the
            // requested resource itself.
            return $this->createUnauthorizedResponse(
                $source,
                $request->getHeader( 'Authorization' )
            );
            // Multistatus with 403 will be issued for nested resources in the
            // specific methods.
        }

        // Check if resource is available
        if ( !$this->nodeExists( $source ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_404,
                $source
            );
        }

        // Verify If-[None-]Match headers
        $res = $this->checkIfMatchHeadersRecursive(
            $request,
            $source,
            $request->getHeader( 'Depth' )
        );
        if ( $res !== null )
        {
            return $res;
        }

        // Check the exact type of propfind request and dispatch to
        // corresponding method.
        switch ( true )
        {
            case $request->prop:
                return $this->fetchProperties( $request );

            case $request->propName:
                return $this->fetchPropertyNames( $request );

            case $request->allProp:
                return $this->fetchAllProperties( $request );
        }

        // This should really never happen, because the request class itself
        // should have ensured, that on of those options is set. Untestable.
        return new ezcWebdavErrorResponse(
            ezcWebdavResponse::STATUS_500
        );
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
     * @param ezcWebdavPropPatchRequest $request
     * @return ezcWebdavResponse
     */
    public function propPatch( ezcWebdavPropPatchRequest $request )
    {
        $source = $request->requestUri;

        // Check authorization
        // Need to do this before checking of node existence is checked, to
        // avoid leaking information
        if ( !ezcWebdavServer::getInstance()->isAuthorized( $source, $request->getHeader( 'Authorization' ), ezcWebdavAuthorizer::ACCESS_WRITE ) )
        {
            return $this->createUnauthorizedResponse(
                $source,
                $request->getHeader( 'Authorization' )
            );
        }

        // Check if resource is available
        if ( !$this->nodeExists( $source ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_404,
                $source
            );
        }

        // Store proeprties, to be able to revert all changes later
        $propertyBackup = clone $this->getAllProperties( $source );

        $errors = array(
            ezcWebdavResponse::STATUS_403 => new ezcWebdavBasicPropertyStorage(),
            ezcWebdavResponse::STATUS_409 => new ezcWebdavBasicPropertyStorage(),
            ezcWebdavResponse::STATUS_424 => new ezcWebdavBasicPropertyStorage(),
        );
        $errnous = false;

        // Update properties, like requested
        foreach ( $request->updates as $property )
        {
            // If there already has been some error, issue failed
            // dependency errors for everything else.
            if ( $errnous )
            {
                $errors[ezcWebdavResponse::STATUS_424]->attach( $property );
                continue;
            }

            // Check for property validation errors and add a 409 for this.
            if ( $property->hasError )
            {
                $errors[ezcWebdavResponse::STATUS_409]->attach( $property );
                $errnous = true;
                continue;
            }

            switch ( $request->updates->getFlag( $property->name, $property->namespace ) )
            {
                case ezcWebdavPropPatchRequest::REMOVE:
                    if ( !$this->removeProperty( $source, $property ) )
                    {
                        // If update failed, we assume the access has been denied.
                        $errors[ezcWebdavResponse::STATUS_403]->attach( $property );
                        $errnous = true;
                    }
                    break;

                case ezcWebdavPropPatchRequest::SET:
                    if ( !$this->setProperty( $source, $property ) )
                    {
                        // If update failed, we assume the access has been denied.
                        // 
                        // @todo: This assumptions is not particular correct.
                        // In case of live properties, which were tried to
                        // update a 409 error would be correct.
                        $errors[ezcWebdavResponse::STATUS_403]->attach( $property );
                        $errnous = true;
                    }
                    break;

                default:
                    // This may happen, when a broken flag has been assigned
                    // during request generation. This SHOULD never happen.
                    $this->resetProperties( $source, $propertyBackup );

                    return new ezcWebdavErrorResponse(  
                        ezcWebdavResponse::STATUS_500
                    );
            }
        }

        // Create node from source for response
        if ( $this->isCollection( $source ) )
        {
            $node = new ezcWebdavCollection( $source );
        }
        else
        {
            $node = new ezcWebdavResource( $source );
        }

        if ( $errnous )
        {
            // Revert all changes
            $this->resetProperties( $source, $propertyBackup );

            // Create response
            return new ezcWebdavMultistatusResponse(
                new ezcWebdavPropPatchResponse(
                    $node,
                    new ezcWebdavPropStatResponse(
                        $errors[ezcWebdavResponse::STATUS_403],
                        ezcWebdavResponse::STATUS_403
                    ),
                    new ezcWebdavPropStatResponse(
                        $errors[ezcWebdavResponse::STATUS_409],
                        ezcWebdavResponse::STATUS_409
                    ),
                    new ezcWebdavPropStatResponse(
                        $errors[ezcWebdavResponse::STATUS_424],
                        ezcWebdavResponse::STATUS_424
                    )
                )
            );
        }
        
        // Verify If-[None-]Match headers.
        // Done in this place to ensure that PROPPATCH would succeed otherwise.
        // Reset of properties to orgiginal state is performed if ETag check
        // fails.
        if ( ( $res = $this->checkIfMatchHeaders( $request, $source ) ) !== null )
        {
            $this->resetProperties( $source, $propertyBackup );
            return $res;
        }

        $successProps = new ezcWebdavBasicPropertyStorage();
        foreach(  $request->updates as $updatedProperty )
        {
            $successProp = clone $updatedProperty;
            $successProp->clear();
            $successProps->attach( $successProp );
        }

        // RFC update requires multi-status even if everything worked properly
        return new ezcWebdavPropPatchResponse(
            $node,
            new ezcWebdavPropStatResponse( $successProps )
        );
    }

    /**
     * Serves PUT requests.
     *
     * The method receives a {@link ezcWebdavPutRequest} objects containing all
     * relevant information obout the clients request and will return an
     * instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavPutResponse} on success.
     * 
     * @param ezcWebdavPutRequest $request 
     * @return ezcWebdavResponse
     */
    public function put( ezcWebdavPutRequest $request )
    {
        $source = $request->requestUri;

        // Check authorization
        // Need to do this before checking of node existence is checked, to
        // avoid leaking information
        if ( !ezcWebdavServer::getInstance()->isAuthorized( $source, $request->getHeader( 'Authorization' ), ezcWebdavAuthorizer::ACCESS_WRITE ) )
        {
            return $this->createUnauthorizedResponse(
                $source,
                $request->getHeader( 'Authorization' )
            );
        }

        // Check if parent node exists and throw a 409 otherwise
        if ( !$this->nodeExists( dirname( $source ) ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_409,
                $source
            );
        }

        // Check if parent node is a collection, and throw a 409 otherwise
        if ( !$this->isCollection( dirname( $source ) ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_409,
                $source
            );
        }

        // Check if resource to be updated or created does not exists already
        // AND is a collection
        if ( $this->nodeExists( $source ) && $this->isCollection( $source ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_409,
                $source
            );
        }

        // @todo: RFC2616 Section 9.6 PUT requires us to send 501 on all
        // Content-* we don't support.

        // Verify If-[None-]Match headers
        if ( $this->nodeExists( $source ) && ( $res = $this->checkIfMatchHeaders( $request, $source ) ) !== null )
        {
            return $res;
        }

        // Everything is OK, create or update resource.
        if ( !$this->nodeExists( $source ) )
        {
            $this->createResource( $source );
        }
        $this->setResourceContents( $source, $request->body );

        $res = new ezcWebdavPutResponse(
            $source
        );

        // Add ETag header
        $res->setHeader( 'ETag', $this->getETag( $source ) );

        // Deliver response
        return $res;
    }

    /**
     * Serves DELETE requests.
     *
     * The method receives a {@link ezcWebdavDeleteRequest} objects containing
     * all relevant information obout the clients request and will return an
     * instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavDeleteResponse} on success.
     * 
     * @param ezcWebdavDeleteRequest $request 
     * @return ezcWebdavResponse
     */
    public function delete( ezcWebdavDeleteRequest $request )
    {
        $source = $request->requestUri;

        // Check authorization
        // Need to do this before checking of node existence is checked, to
        // avoid leaking information
        $authState = $this->recursiveAuthCheck(
            $request,
            $source,
            ezcWebdavAuthorizer::ACCESS_WRITE,
            true
        );
        if ( count( $authState['errors'] ) !== 0 )
        {
            return $authState['errors'][0];
        }

        // Check if resource is available
        if ( !$this->nodeExists( $source ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_404,
                $source
            );
        }

        // Verify If-[None-]Match headers
        // @todo: Does this make sense for PROPFIND requests?
        $res = $this->checkIfMatchHeadersRecursive(
            $request,
            $source,
            ezcWebdavRequest::DEPTH_INFINITY
        );
        if ( $res !== null )
        {
            return $res;
        }

        // Delete
        $deletion = $this->performDelete( $source );
        if ( $deletion !== null )
        {
            // ezcWebdavMultistatusResponse
            return $deletion;
        }

        // Send proper response on success
        return new ezcWebdavDeleteResponse(
            $source
        );
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
     * @param ezcWebdavCopyRequest $request 
     * @return ezcWebdavResponse
     */
    public function copy( ezcWebdavCopyRequest $request )
    {
        // Indicates wheather a destiantion resource has been replaced or not.
        // The success response code depends on this.
        $replaced = false;

        // Extract paths from request
        $source = $request->requestUri;
        $dest = $request->getHeader( 'Destination' );
        
        // Check authorization
        // Need to do this before checking of node existence is checked, to
        // avoid leaking information 
        if ( !ezcWebdavServer::getInstance()->isAuthorized( $source, $request->getHeader( 'Authorization' ) ) )
        {
            return $this->createUnauthorizedResponse(
                $source,
                $request->getHeader( 'Authorization' )
            );
        }
        if ( !ezcWebdavServer::getInstance()->isAuthorized( $dest, $request->getHeader( 'Authorization' ), ezcWebdavAuthorizer::ACCESS_WRITE ) )
        {
            return $this->createUnauthorizedResponse(
                $dest,
                $request->getHeader( 'Authorization' )
            );
        }

        // Check if resource is available
        if ( !$this->nodeExists( $source ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_404,
                $source
            );
        }

        // If source and destination are equal, the request should always fail.
        if ( $source === $dest )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_403,
                $source
            );
        }

        // Check if destination resource exists and throw error, when
        // overwrite header is F
        if ( ( $request->getHeader( 'Overwrite' ) === 'F' ) &&
             $this->nodeExists( $dest ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_412,
                $dest
            );
        }

        // Check if the destination parent directory already exists, otherwise
        // bail out.
        if ( !$this->nodeExists( $destDir = dirname( $dest ) ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_409,
                $dest
            );
        }

        // Verify If-[None-]Match headers on the $source
        $res = $this->checkIfMatchHeadersRecursive(
            $request,
            $source,
            $request->getHeader( 'Depth' )
        );
        if ( $res !== null )
        {
            return $res;
        }
        
        // Verify If-[None-]Match headers on the $dest if it exists
        if ( $this->nodeExists( $dest ) &&
             ( $res = $this->checkIfMatchHeaders( $request, $dest ) ) !== null
           )
        {
            return $res;
        }
        // Verify If-[None-]Match headers on the on $dests parent dir, if it
        // does not exist
        elseif ( ( $res = $this->checkIfMatchHeaders( $request, $destDir ) ) !== null )
        {
            return $res;
        }

        // The destination resource should be deleted if it exists and the
        // overwrite headers is T
        if ( ( $request->getHeader( 'Overwrite' ) === 'T' ) &&
             $this->nodeExists( $dest ) )
        {
            // Check sub-sequent authorization on destination
            $authState = $this->recursiveAuthCheck(
                $request,
                $dest,
                ezcWebdavAuthorizer::ACCESS_WRITE,
                true
            );
            if ( count( $authState['errors'] ) !== 0 )
            {
                // Permission denied on deleting destination
                return $authState['errors'][0];
            }

            // Perform delete
            // @todo: This method might return errors. If it does, the delete
            // was not successful and therefore no copy should happen! (see:
            // move()).
            $replaced = true;
            $this->performDelete( $dest );
        }

        $errors    = array();
        $copyPaths = array();
        
        if ( $request->getHeader( 'Depth' ) === ezcWebdavRequest::DEPTH_INFINITY )
        {
            $authState = $this->recursiveAuthCheck( $request, $source );
            $errors    = $authState['errors'];
            $copyPaths = $authState['paths'];
        }
        else
        {
            // Non recursive auth check necessary, plain check on $source
            // already performed
            $copyPaths = array( $source => ezcWebdavRequest::DEPTH_ZERO );
        }

        // Recursively copy paths that should be copied
        foreach ( $copyPaths as $copySource => $copyDepth )
        {
            // Build destination path fur descendants
            $copyDest = $dest . (string) substr( $copySource, strlen( $source ) );
            // Perform copy and collect additional errors.
            $errors = array_merge( 
                $errors,
                // @todo: handle keepalive setting somehow - even the RFC is quite
                // vague how to handle them exactly.
                $this->performCopy( $copySource, $copyDest, $copyDepth )
            );
        }

        if ( !count( $errors ) )
        {
            // No errors occured during copy. Just response with success.
            return new ezcWebdavCopyResponse(
                $replaced
            );
        }

        // Send proper response on success
        return new ezcWebdavMultistatusResponse( $errors );
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
     * @param ezcWebdavMoveRequest $request 
     * @return ezcWebdavResponse
     */
    public function move( ezcWebdavMoveRequest $request )
    {
        // Indicates wheather a destiantion resource has been replaced or not.
        // The success response code depends on this.
        $replaced = false;

        // Extract paths from request
        $source = $request->requestUri;
        $dest = $request->getHeader( 'Destination' );

        // Check authorization
        // Need to do this before checking of node existence is checked, to
        // avoid leaking information 
        $authState = $this->recursiveAuthCheck(
            $request,
            $dest,
            ezcWebdavAuthorizer::ACCESS_WRITE,
            true
        );
        if ( count( $authState['errors'] ) !== 0 )
        {
            // Source permission denied
            return $authState['errors'][0];
        }
        if ( !ezcWebdavServer::getInstance()->isAuthorized( $dest, $request->getHeader( 'Authorization' ), ezcWebdavAuthorizer::ACCESS_WRITE ) )
        {
            return $this->createUnauthorizedResponse(
                $dest,
                $request->getHeader( 'Authorization' )
            );
        }

        // Check if resource is available
        if ( !$this->nodeExists( $source ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_404,
                $source
            );
        }

        // If source and destination are equal, the request should always fail.
        if ( $source === $dest )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_403,
                $source
            );
        }

        // Check if destination resource exists and throw error, when
        // overwrite header is F
        if ( ( $request->getHeader( 'Overwrite' ) === 'F' ) &&
             $this->nodeExists( $dest ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_412,
                $dest
            );
        }

        // Check if the destination parent directory already exists, otherwise
        // bail out.
        if ( !$this->nodeExists( $destDir = dirname( $dest ) ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_409,
                $dest
            );
        }
        
        // Verify If-[None-]Match headers on the $source
        $res = $this->checkIfMatchHeadersRecursive(
            $request,
            $source,
            // We move, not copy!
            ezcWebdavRequest::DEPTH_INFINITY
        );
        if ( $res !== null )
        {
            return $res;
        }
        
        // Verify If-[None-]Match headers on the $dest if it exists
        if ( $this->nodeExists( $dest ) &&
             ( $res = $this->checkIfMatchHeaders( $request, $dest ) ) !== null
           )
        {
            return $res;
        }
        // Verify If-[None-]Match headers on the on $dests parent dir, if it
        // does not exist
        elseif ( ( $res = $this->checkIfMatchHeaders( $request, $destDir ) ) !== null )
        {
            return $res;
        }

        // The destination resource should be deleted if it exists and the
        // overwrite headers is T
        if ( ( $request->getHeader( 'Overwrite' ) === 'T' ) &&
             $this->nodeExists( $dest ) )
        {
            // Check sub-sequent authorization on destination
            $authState = $this->recursiveAuthCheck(
                $request,
                $dest,
                ezcWebdavAuthorizer::ACCESS_WRITE,
                true
            );
            if ( count( $authState['errors'] ) !== 0 )
            {
                // Permission denied on deleting destination
                return $authState['errors'][0];
            }

            $replaced = true;

            if ( count( $delteErrors = $this->performDelete( $dest ) ) > 0 )
            {
                return new ezcWebdavMultistatusResponse( $delteErrors );
            }
        }

        // All checks are passed, we can actuall copy now.
        // 
        // MOVEd contents should always be copied using infinity depth.
        // 
        // @todo: handle keepalive setting somehow - even the RFC is quite
        // vague how to handle them exactly.
        $errors = $this->performCopy( $source, $dest, ezcWebdavRequest::DEPTH_INFINITY );

        // If an error occured we skip deletion of source.
        // 
        // @IMPORTANT: This is a definition / assumption made by us, because it
        // is not defined in the RFC how to handle such a case.
        if ( count( $errors ) )
        {
            // We need a multistatus response, because some errors occured for some
            // of the resources.
            return new ezcWebdavMultistatusResponse( $errors );
        }

        // Delete the source, COPY has been successful
        $deletion = $this->performDelete( $source );

        // If deletion failed, this has again been caused by the automatic
        // error causing facilities of the backend. Send 423 by choice.
        // 
        // @todo: The error generated here should depend on the actual backend
        // implementation and  not be generated guessing what may fit.
        if ( count( $deletion ) > 0 )
        {
            return new ezcWebdavMultistatusResponse( $deletion );
        }

        // Send proper response on success
        return new ezcWebdavMoveResponse(
            $replaced
        );
    }

    /**
     * Serves MKCOL (make collection) requests.
     *
     * The method receives a {@link ezcWebdavMakeCollectionRequest} objects
     * containing all relevant information obout the clients request and will
     * return an instance of {@link ezcWebdavErrorResponse} on error or {@link
     * ezcWebdavMakeCollectionResponse} on success.
     * 
     * @param ezcWebdavMakeCollectionRequest $request 
     * @return ezcWebdavResponse
     */
    public function makeCollection( ezcWebdavMakeCollectionRequest $request )
    {
        $collection = $request->requestUri;

        // Check authorization
        // Need to do this before checking of node existence is checked, to
        // avoid leaking information
        if ( !ezcWebdavServer::getInstance()->isAuthorized( $collection, $request->getHeader( 'Authorization' ), ezcWebdavAuthorizer::ACCESS_WRITE ) )
        {
            return $this->createUnauthorizedResponse(
                $collection,
                $request->getHeader( 'Authorization' )
            );
        }

        // If resource already exists, the collection cannot be created and a
        // 405 is thrown.
        if ( $this->nodeExists( $collection ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_405,
                $collection
            );
        }

        // Check if the parent node already exists, otherwise throw a 409
        // error.
        if ( !$this->nodeExists( dirname( $collection ) ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_409,
                $collection
            );
        }

        // If the parent node exists, but is a resource, which obviously can
        // not accept any members, throw a 403 error.
        if ( !$this->isCollection( $destDir = dirname( $collection ) ) )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_403,
                $collection
            );
        }

        // Verify If-[None-]Match headers on the on $dests parent dir
        if ( ( $res = $this->checkIfMatchHeaders( $request, $destDir ) ) !== null )
        {
            return $res;
        }

        // As the handling of request bodies is not described in RFC 2518, we
        // skip their handling and always return a 415 error.
        if ( $request->body )
        {
            return new ezcWebdavErrorResponse(
                ezcWebdavResponse::STATUS_415,
                $collection
            );
        }

        // Cause error, if requested?

        // All checks passed, we can create the collection
        $this->createCollection( $collection );

        // Return success
        return new ezcWebdavMakeCollectionResponse(
            $collection
        );
    }

    /**
     * Handles the OPTIONS request.
     *
     * Applies authorization checking to the OPTIONS request and returns the
     * parent response.
     * 
     * @param ezcWebdavOptionsRequest $request 
     * @return ezcWebdavOptionsResponse
     */
    public function options( ezcWebdavOptionsRequest $request )
    {
        // Check authorization
        if ( !ezcWebdavServer::getInstance()->isAuthorized( $request->requestUri, $request->getHeader( 'Authorization' ) ) )
        {
            return $this->createUnauthorizedResponse(
                $request->requestUri,
                $request->getHeader( 'Authorization' )
            );
        }
        
        return parent::options( $request );
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
        $contentLength = $this->getProperty( $path, 'getcontentlength' )->length;
        $lastModified  = $this->getProperty( $path, 'getlastmodified' )->date->format( 'c' );

        return md5( $path . $contentLength . $lastModified );
    }

    /**
     * Checks If-[Match-]Headers recursively on $path with $depth.
     *
     * Performs a recursive check using {@link checkIfMatchHeaders()} on $path.
     * $depth can be any of the {@link ezcWebdavRequest} DEPTH_* constants.
     *
     * Returns {@link ezcWebdavErrorResponse} if any ETag check failed, null if
     * everything went allright.
     * 
     * @param ezcWebdavRequest $req 
     * @param string $path 
     * @param int $depth 
     * @return ezcWebdavErrorResponse|null
     */
    private function checkIfMatchHeadersRecursive( ezcWebdavRequest $req, $path, $depth )
    {
        // Stop checking if non-collection is reached or depth is 0.
        if ( !$this->isCollection( $path ) || $depth === ezcWebdavRequest::DEPTH_ZERO )
        {
            return $this->checkIfMatchHeaders( $req, $path );
        }

        // Check collection ETag
        if ( ( $res = $this->checkIfMatchHeaders( $req, $path ) ) !== null )
        {
            return $res;
        }

        // $path is a collection, depth is > 0: Recurse.
        $newDepth = $depth === ezcWebdavRequest::DEPTH_ONE
            ? ezcWebdavRequest::DEPTH_ZERO
            : ezcWebdavRequest::DEPTH_INFINITY;

        foreach ( $this->getCollectionMembers( $path ) as $member )
        {
            // If-[None-]Match header check produced error.
            if ( ( $res = $this->checkIfMatchHeadersRecursive( $req, $member->path, $newDepth ) ) !== null )
            {
                return $res;
            }
        }
        return null;
    }

    /**
     * Checks the If-Match and If-None-Match headers.
     *
     * Checks if the If-Match and If-None-Match headers (potentially) provided
     * by $req are valid for the resource identified by $path.
     *
     * Returns null if everything is alright, ezcWebdavErrorResponse if any
     * ETag check failed.
     * 
     * @param ezcWebdavRequest $req 
     * @param string $path 
     * @return ezcWebdavErrorResponse|null
     */
    protected function checkIfMatchHeaders( ezcWebdavRequest $req, $path )
    {
        if ( ( $res = $this->checkIfMatchHeader( $req, $path ) ) !== null )
        {
            return $res;
        }
        if ( ( $res = $this->checkIfNoneMatchHeader( $req, $path ) ) !== null )
        {
            return $res;
        }
        return null;
    }

    /**
     * Checks the If-Match-Header on $path, if present in $req.
     *
     * Returns ezcWebdavErrorResponse on failure, otherwise null.
     * 
     * @param ezcWebdavRequest $req 
     * @param string $path 
     * @return ezcWebdavErrorResponse|null
     */
    private function checkIfMatchHeader( ezcWebdavRequest $req, $path )
    {
        if ( ( $matches = $req->getHeader( 'If-Match' ) ) !== null )
        {
            $etag = $this->getETag( $path );
            if ( $this->checkMatchHeader( $matches, $etag ) === false )
            {
                return new ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_412,
                    $path,
                    'If-Match header check failed.'
                );
            }
        }
        return null;
    }

    /**
     * Checks the If-Match-Header on $path, if present in $req.
     *
     * Returns ezcWebdavErrorResponse on failure, otherwise null.
     * 
     * @param ezcWebdavRequest $req 
     * @param string $path 
     * @return ezcWebdavErrorResponse|null
     */
    private function checkIfNoneMatchHeader( ezcWebdavRequest $req, $path )
    {
        if ( ( $matches = $req->getHeader( 'If-None-Match' ) ) !== null )
        {
            $etag = $this->getETag( $path );
            if ( $this->checkMatchHeader( $matches, $etag ) === true )
            {
                return new ezcWebdavErrorResponse(
                    ezcWebdavResponse::STATUS_412,
                    $path,
                    'If-None-Match header check failed.'
                );
            }
        }
        return null;
    }

    /**
     * Checks the If-[None-]Match header values against an $etag.
     *
     * Returns in any of the ETags given in $matches euqlauls to $etag.
     *
     * This is used in {@link checkIfMatchHeader()} and {@link
     * checkIfNoneMatchHeader()}. The {@link checkIfMatchHeader()} method
     * expects true as a good result, while {@link checkIfNoneMatchHeader()}
     * desires false.
     * 
     * @param array(string) $matches 
     * @param string $etag 
     * @return bool
     */
    private function checkMatchHeader( $matches, $etag )
    {
        if ( $matches === true )
        {
            return true;
        }
        foreach ( $matches as $testEtag )
        {
            if ( $etag === $testEtag )
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Recursively checks authorization for the COPY, MOVE and other requests.
     *
     * This method performs a recursive authorization check on the given $path
     * using the credentials provided in $request. It returns a
     * multidimensional array, indicating the authorization errors occurred and
     * the paths that may by copied.
     *
     * The structure looks like this:
     * <code>
     * array(
     *      'errors' => array(
     *          ezcWebdavErrorResponse(),
     *          ezcWebdavErrorResponse(),
     *          // ...
     *      )
     *      'paths' => array(
     *          '/some/path' => ezcWebdavRequest::DEPTH_INFINITY,
     *          '/some/other/path' => ezcWebdavRequest::DEPTH_ZERO,
     *          // ...
     *      )
     * )
     * </code>
     *
     * The 'errors' key is assigned to an array of authorization error
     * responses that will be merged to the ezcWebdavMultistatusResponse
     * returned by the copy() method. The 'paths' array contains all paths that
     * may be copied by the method. A path is assigned to the depth that it
     * might be copied. The depth can be {@link
     * ezcWebdavRequest::DEPTH_INFINITY} to indicate that a complete sub tree
     * is save for copying, or {@link ezcWebdavRequest::DEPTH_ZERO}, to
     * indicate that only the path itself may be copied, but none of its
     * descendants.
     *
     * The $access parameter specifies which permission is to be checked {@link
     * ezcWebdavAuthorizer::ACCESS_READ} is the default, {@link
     * ezcWebdavAuthorizer::ACCESS_WRITE} may be set to indicate write
     * permissions.
     *
     * If the $breakOnError parameter is set to true, no further checks will be
     * applied to sibling resources, but the method will instantly return. This
     * parameter is set to true for the MOVE request, since this request must
     * be processed completly or not at all. The COPY request in contrast may
     * also be processed partially, so this parameter is left as is.
     * 
     * @param ezcWebdavRequest $request 
     * @param string $path 
     * @param int $access
     * @param bool $breakOnError
     * @return array
     *
     * @todo Mark protected as soon as API is final.
     */
    private function recursiveAuthCheck( ezcWebdavRequest $request, $path, $access = ezcWebdavAuthorizer::ACCESS_WRITE, $breakOnError = false )
    {
        $result = array(
            'errors' => array(),
            'paths' => array(),
        );

        // Check auth for collections and resources equally
        if ( !ezcWebdavServer::getInstance()->isAuthorized( $path, $request->getHeader( 'Authorization' ), $access ) )
        {
            $result['errors'][] = $this->createUnauthorizedResponse(
                $path,
                $request->getHeader( 'Authorization' )
            );
        }
        else
        {
            if ( $this->isCollection( $path ) )
            {
                foreach ( $this->getCollectionMembers( $path ) as $member )
                {
                    $tmpRes = $this->recursiveAuthCheck( $request, $member->path, $access );
                    if ( count( $tmpRes['errors'] ) !== 0 )
                    {
                        if ( $breakOnError )
                        {
                            return $tmpRes;
                        }
                        $result['errors'] = array_merge( $result['errors'], $tmpRes['errors'] );
                        $result['paths']  = array_merge( $result['paths'],  $tmpRes['paths'] );
                    }
                }
                $result['paths'][$path] = ( count( $result['errors'] ) ? ezcWebdavRequest::DEPTH_ZERO : ezcWebdavRequest::DEPTH_INFINITY );
            }
            else
            {
                // Only a resource, so depth infinity does not make sense
                $result['paths'][$path] = ezcWebdavRequest::DEPTH_ZERO;
            }
        }

        return $result;
    }

    /**
     * Returns an error response to indicate failed authorization.
     *
     * This method returns an instance of {@link ezcWebdavErrorResponse} with a
     * corresponding status code, indicating that the request to $path was not
     * authorized. In case the user did not provide authentication at all, the
     * status code 401 (Unauthorized) is used to give the possibility of
     * authenticating. Otherwise 403 (Forbidden) is used, since the
     * authenticated user simply does not have access.
     * 
     * @param string $path 
     * @param ezcWebdavAuthBasic|ezcWebdavAúthDigest $authHeader
     * @return ezcWebdavErrorResponse
     */
    private function createUnauthorizedResponse( $path, $authHeader = null )
    {
        // Check for anonymous auth
        if ( $authHeader === null || $authHeader->username === '' )
        {
            return ezcWebdavServer::getInstance()->createUnauthenticatedResponse(
                $path,
                'Authorization failed.'
            );
        }

        // Authenticated user does not have access
        return ezcWebdavServer::getInstance()->createUnauthorizedResponse(
            $path,
            'Authorization failed.'
        );
    }
}

?>
