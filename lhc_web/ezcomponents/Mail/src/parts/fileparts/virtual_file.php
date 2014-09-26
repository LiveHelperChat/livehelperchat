<?php
/**
 * File containing the ezcMailVirtualFile class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Mail part for binary data in memory.
 *
 * @property string $contents
 *           The contents to be added as an attachment. The mimeType and
 *           contentType are set in the constructor or if not specified they
 *           are extracted with the fileinfo extension if it is available,
 *           otherwise they are set to application/octet-stream.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailVirtualFile extends ezcMailFilePart
{
    /**
     * Constructs a new attachment with $fileName and $contents.
     *
     * If the $mimeType and $contentType are not specified they are extracted
     * with the fileinfo extension if it is available, otherwise they are set
     * to application/octet-stream.
     *
     * @param string $fileName
     * @param string $contents
     * @param string $contentType
     * @param string $mimeType
     */
    public function __construct( $fileName, $contents, $contentType = null, $mimeType = null )
    {
        parent::__construct( $fileName );
        $this->contents = $contents;

        if ( $contentType != null && $mimeType != null )
        {
            $this->contentType = $contentType;
            $this->mimeType = $mimeType;
        }
        elseif ( ezcBaseFeatures::hasExtensionSupport( 'fileinfo' ) )
        {
            // get mime and content type
            $fileInfo = new finfo( FILEINFO_MIME );
            $mimeParts = $fileInfo->buffer( $contents );
            if ( $mimeParts !== false && strpos( $mimeParts, '/' ) !== false )
            {
                list( $this->contentType, $this->mimeType ) = explode( '/', $mimeParts );
            }
            else
            {
                // default to mimetype application/octet-stream
                $this->contentType = self::CONTENT_TYPE_APPLICATION;
                $this->mimeType = "octet-stream";
            }
        }
        else
        {
            // default to mimetype application/octet-stream
            $this->contentType = self::CONTENT_TYPE_APPLICATION;
            $this->mimeType = "octet-stream";
        }
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'contents':
                $this->properties[$name] = $value;
                break;
            default:
                return parent::__set( $name, $value );
                break;
        }
    }

    /**
     * Returns the value of property $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist.
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'contents':
                return $this->properties[$name];
                break;
            default:
                return parent::__get( $name );
                break;
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'contents':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Returns the contents of the file with the correct encoding.
     *
     * @return string
     */
    public function generateBody()
    {
        return chunk_split( base64_encode( $this->contents ), 76, ezcMailTools::lineBreak() );
    }
}
?>
