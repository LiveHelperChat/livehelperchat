<?php
/**
 * File containing the ezcMailFilePart class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Mail part for all forms of binary data.
 *
 * @todo MimeType recognition
 *
 * @property string $fileName
 *           The name of the file which is to be attached to the email.
 * @property string $mimeType
 *           The mimetype of the file.
 * @property string $contentType
 *           The content type of the file.
 *           Possible values are: CONTENT_TYPE_IMAGE, CONTENT_TYPE_VIDEO and
 *           CONTENT_TYPE_APPLICATION.
 * @property string $dispositionType
 *           If the file should be shown inline in the mail or as an
 *           attachment. Possible values are: DISPLAY_ATTACHMENT and
 *           DISPLAY_INLINE.
 * @property int $contentId
 *           The ID of this part. Used for internal links within an email.
 *           Setting this also sets the header Content-ID.
 *
 * @package Mail
 * @version 1.7.1
 */
abstract class ezcMailFilePart extends ezcMailPart
{
    /**
     * Image content type. Use this if the contents of the file is an image.
     */
    const CONTENT_TYPE_IMAGE = "image";

    /**
     * Video content type. Use this if the contents of the file is a video.
     */
    const CONTENT_TYPE_VIDEO = "video";

    /**
     * Audio content type. Use this if the contents of the file is an audio.
     */
    const CONTENT_TYPE_AUDIO = "audio";

    /**
     * Application content type. Use this if the file non of the other
     * content types match.
     */
    const CONTENT_TYPE_APPLICATION = "application";

    /**
     * Use DISPLAY_ATTACHMENT if you want the file to be displayed as an
     * attachment to the recipients of the mail.
     */
    const DISPLAY_ATTACHMENT = "attachment";

    /**
     * Use DISPLAY_INLINE if you want the file to be displayed inline in the
     * mail to the recipients.
     */
    const DISPLAY_INLINE = "inline";

    /**
     * Constructs a new attachment with $fileName.
     *
     * @param string $fileName
     */
    public function __construct( $fileName )
    {
        parent::__construct();

        // initialize properties that may be touched automatically
        // this is to avoid notices
        $this->properties['contentType'] = null;
        $this->properties['mimeType'] = null;
        $this->properties['dispositionType'] = null;
        $this->properties['contentId'] = null;

        $this->fileName = $fileName;
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
            case 'fileName':
                $this->properties['fileName'] = $value;
                break;

            case 'mimeType':
                $this->properties['mimeType'] = $value;
                break;

            case 'contentType':
                $this->properties['contentType'] = $value;
                break;

            case 'dispositionType':
                $this->properties['dispositionType'] = $value;
                break;

            case 'contentId':
                $this->properties['contentId'] = $value;
                $this->setHeader( 'Content-ID', '<' . $value . '>' );
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
            case 'fileName':
            case 'mimeType':
            case 'contentType':
            case 'dispositionType':
            case 'contentId':
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
            case 'fileName':
            case 'mimeType':
            case 'contentType':
            case 'dispositionType':
            case 'contentId':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Sets the Content-Type header.
     *
     * Based on the contentType, mimeType and fileName.
     */
    private function setHeaderContentType()
    {
        $fileName = basename( $this->fileName );
        if ( $this->contentDisposition !== null && $this->contentDisposition->fileName !== null )
        {
            $fileName = $this->contentDisposition->fileName;
        }

        $this->setHeader( 'Content-Type',
                          $this->contentType . '/' . $this->mimeType . '; ' . 'name="' . $fileName . '"' );
    }

    /**
     * Sets the Content-Disposition header based on the properties $dispositionType and $fileName.
     *
     * Does not set the fileNameCharSet and fileNameLanguage properties of the
     * Content-Disposition header. For this purpose set directly
     * $this->contentDisposition with an object of class ezcMailContentDispositionHeader.
     */
    private function setHeaderContentDisposition()
    {
        if ( !isset( $this->dispositionType ) )
        {
            $this->dispositionType = self::DISPLAY_ATTACHMENT;
        }
        if ( $this->contentDisposition == null )
        {
            $this->contentDisposition = new ezcMailContentDispositionHeader();

            // modified for issue #14025: set the file name and disposition
            // only if the contentDisposition was null (to not overwrite
            // the value set by the user)
            $this->contentDisposition->disposition = $this->dispositionType;
            $this->contentDisposition->fileName = basename( $this->fileName );
        }
    }

    /**
     * Override of the generate() method from ezcMailPart. Used to set headers before
     * generating the part.
     *
     * @return string
     */
    public function generate()
    {
        $this->setHeaderContentType();
        $this->setHeader( 'Content-Transfer-Encoding', 'base64' );
        $this->setHeaderContentDisposition();
        return parent::generate();
    }
}
?>
