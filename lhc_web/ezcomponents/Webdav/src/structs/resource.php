<?php
/**
 * File containing the ezcWebdavResource struct.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Struct class representing a non-collection resource.
 *
 * This struct is used to represent non-collection resources, in contrast to
 * {@link ezcWebdavCollection}, which represents collection resources.
 *
 * @version 1.1.4
 * @package Webdav
 */
class ezcWebdavResource extends ezcBaseStruct
{
    /**
     * Path to resource
     * 
     * @var string
     */
    public $path;

    /**
     * Resource contents
     * 
     * @var string
     */
    public $content;

    /**
     * Live properties of resource.
     * 
     * @var ezcWebdavPropertyStorage
     */
    public $liveProperties;

    /**
     * Creates a new non-collection resource struct.
     * 
     * A new non-collection resource struct is crenated, representing the
     * resource referenced by $path, with the given $liveProperties and
     * $content.
     *
     * @param string $path 
     * @param ezcWebdavPropertyStorage $liveProperties 
     * @param string $content 
     * @return void
     */
    public function __construct( $path, ezcWebdavPropertyStorage $liveProperties = null, $content = null )
    {
        $this->path           = $path;
        $this->liveProperties = $liveProperties;
        $this->content        = $content;
    }
}

?>
