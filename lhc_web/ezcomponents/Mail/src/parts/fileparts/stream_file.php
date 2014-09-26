<?php
/**
 * File containing the ezcMailStreamFile class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Mail part for data in a stream.
 *
 * @property string $stream
 *           The stream object to be read and added as an attachment. The
 *           mimeType and contentType are set in the constructor or if not
 *           specified they are extracted with the fileinfo extension if it
 *           is available, otherwise they are set to application/octet-stream.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailStreamFile extends ezcMailFilePart
{
    /**
     * Constructs a new attachment with $fileName and $stream.
     *
     * If the $mimeType and $contentType are not specified they are set
     * to application/octet-stream.
     *
     * @param string $fileName
     * @param resource $stream
     * @param string $contentType
     * @param string $mimeType
     */
    public function __construct( $fileName, $stream, $contentType = null, $mimeType = null )
    {
        parent::__construct( $fileName );
        $this->stream = $stream;
        if ( $contentType != null && $mimeType != null )
        {
            $this->contentType = $contentType;
            $this->mimeType = $mimeType;
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
            case 'stream':
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
            case 'stream':
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
            case 'stream':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Returns the contents of the file with the correct encoding.
     *
     * The stream might become unusable after this if it doesn't support seek.
     *
     * @return string
     */
    public function generateBody()
    {
        $contents = stream_get_contents( $this->stream );
        return chunk_split( base64_encode( $contents ), 76, ezcMailTools::lineBreak() );
    }
}
?>
