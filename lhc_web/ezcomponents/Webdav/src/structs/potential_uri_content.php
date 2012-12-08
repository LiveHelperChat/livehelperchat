<?php
/**
 * File containing the ezcWebdavPotentialUriContent struct.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Struct representing a text that is potentially considered an URI.
 *
 * Some Webdav property values might either contain plain text or an URI,
 * covered in an <href> XML element. This struct is used to represent such
 * information. If the content of the property is an URI, the $isUri property
 * is set to true. Otherwise it is false. The $content property contains the
 * plain text content.
 * 
 * @package Webdav
 * @version 1.1.4
 */
class ezcWebdavPotentialUriContent extends ezcBaseStruct
{
    /**
     * Text content.
     * 
     * @var string
     */
    public $content;

    /**
     * If the text content is to be considered an URI. 
     * 
     * @var bool
     */
    public $isUri;

    /**
     * Creates a new potential URI content struct.
     * 
     * @param string $content 
     * @param bool $isUri 
     */
    public function __construct( $content = '', $isUri = false )
    {
        $this->content = $content;
        $this->isUri   = $isUri;
    }

    /**
     * Converts the object to a string.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }
}

?>
