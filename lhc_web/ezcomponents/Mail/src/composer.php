<?php
/**
 * File containing the ezcMailComposer class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Convenience class for writing mail.
 *
 * This class allows you to create
 * text and/or HTML mail with attachments. If you need to create more
 * advanced mail use the ezcMail class and build the body from scratch.
 *
 * ezcMailComposer is used with the following steps:
 * 1. Create a composer object.
 * 2. Set the subject and recipients.
 * 3. Set the plainText and htmlText message parts. You can set only one
 *    or both. If you set both, the client will display the htmlText if it
 *    supports HTML. Otherwise the client will display plainText.
 * 4. Add any attachments (addFileAttachment() or addStringAttachment()).
 * 5. Call the build method.
 *
 * This example shows how to send an HTML mail with a text fallback and
 * attachments. The HTML message has an inline image.
 * <code>
 * $mail = new ezcMailComposer();
 * $mail->from = new ezcMailAddress( 'john@example.com', 'John Doe' );
 * $mail->addTo( new ezcMailAddress( 'cindy@example.com', 'Cindy Doe' ) );
 * $mail->subject = "Example of an HTML email with attachments";
 * $mail->plainText = "Here is the text version of the mail. This is displayed if the client can not understand HTML";
 * $mail->htmlText = "<html>Here is the HTML version of your mail with an image: <img src='file://path_to_image.jpg' /></html>";
 * $mail->addFileAttachment( 'path_to_attachment.file' );
 * $mail->build();
 * $transport = new ezcMailMtaTransport();
 * $transport->send( $mail );
 * </code>
 *
 * By default, if the htmlText property contains an HTML image tag with file://
 * in href, that file will be included in the created message.
 *
 * Example:
 * <code>
 * <img src="file:///home/me/image.jpg" />
 * </code>
 *
 * This can be a security risk if a user links to another file, for example logs
 * or password files. With the automaticImageInclude option (default value true)
 * from {@link ezcMailComposerOptions}, the automatic inclusion of files can be
 * turned off.
 *
 * Example:
 * <code>
 * $options = new ezcMailComposerOptions();
 * $options->automaticImageInclude = false; // default value is true
 *
 * $mail = new ezcMailComposer( $options );
 *
 * // ... add To, From, Subject, etc to $mail
 * $mail->htmlText = "<html>Here is the image: <img src="file:///etc/passwd" /></html>";
 *
 * // ... send $mail
 * </code>
 *
 * After running the above code, the sent mail will not contain the file specified
 * in the htmlText property.
 *
 * The file name in the attachment can be different than the file name on disk, by
 * passing an {@link ezcMailContentDispositionHeader} object to the function
 * addFileAttachment(). Example:
 * <code>
 * $mail = new ezcMailComposer();
 * $mail->from = new ezcMailAddress( 'john@example.com', 'John Doe' );
 * $mail->addTo( new ezcMailAddress( 'cindy@example.com', 'Cindy Doe' ) );
 * $mail->subject = "Example of an HTML email with attachments and custom attachment file name";
 * $mail->plainText = "Here is the text version of the mail. This is displayed if the client can not understand HTML";
 * $mail->htmlText = "<html>Here is the HTML version of your mail with an image: <img src='file://path_to_image.jpg' /></html>";
 *
 * $disposition = new ezcMailContentDispositionHeader();
 * $disposition->fileName = 'custom name for attachment.txt';
 * $disposition->fileNameCharSet = 'utf-8'; // if using non-ascii characters in the file name
 * $disposition->disposition = 'attachment'; // default value is 'inline'
 *
 * $mail->addFileAttachment( 'path_to_attachment.file', null, null, $disposition );
 * $mail->build();
 *
 * $transport = new ezcMailMtaTransport();
 * $transport->send( $mail );
 * </code>
 *
 * Use the function addStringAttachment() if you want to add an attachment which
 * is stored in a string variable. A file name for a string attachment needs to
 * be specified as well. Example:
 *
 * <code>
 * $contents = 'contents for mail attachment'; // can be a binary string, eg. image file contents
 * $mail->addStringAttachment( 'filename', $contents );
 * </code>
 *
 * @property string $plainText
 *           Contains the message of the mail in plain text.
 * @property string $htmlText
 *           Contains the message of the mail in HTML. You should also provide
 *           the text of the HTML message in the plainText property. Both will
 *           be sent and the receiver will see the HTML message if his/her
 *           client supports HTML.  If the HTML message contains links to
 *           local images and/or files these will be included into the mail
 *           when generateBody is called. Links to local files must start with
 *           "file://" in order to be recognized. You can use the option
 *           automaticImageInclude (default value is true) from
 *           {@link ezcMailComposerOptions} to turn off the
 *           automatic inclusion of files in the generated mail.
 * @property string $charset
 *           Contains the character set for both $plainText and $htmlText.
 *           Default value is 'us-ascii'. This does not set any specific
 *           charset for the subject, you need the subjectCharset property for
 *           that.
 * @property string $encoding
 *           Contains the encoding for both $plainText and $htmlText.
 *           Default value is ezcMail::EIGHT_BIT. Other values are found
 *           as constants in the class {@link ezcMail}.
 * @property ezcMailComposerOptions $options
 *           Options for composing mail. See {@link ezcMailComposerOptions}.
 *
 * @package Mail
 * @version 1.7.1
 * @mainclass
 */
class ezcMailComposer extends ezcMail
{
    /**
     * Holds the attachments filenames.
     *
     * The array contains relative or absolute paths to the attachments.
     *
     * @var array(string)
     */
    private $attachments = array();

    /**
     * Holds the options for this class.
     *
     * @var ezcMailComposerOptions
     */
    protected $options;

    /**
     * Constructs an empty ezcMailComposer object.
     *
     * @param ezcMailComposerOptions $options
     */
    public function __construct( ezcMailComposerOptions $options = null )
    {
        $this->properties['plainText'] = null;
        $this->properties['htmlText'] = null;
        $this->properties['charset'] = 'us-ascii';
        $this->properties['encoding'] = ezcMail::EIGHT_BIT;
        if ( $options === null )
        {
            $options = new ezcMailComposerOptions();
        }

        $this->options = $options;

        parent::__construct( $options );
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
            case 'plainText':
            case 'htmlText':
            case 'charset':
            case 'encoding':
                $this->properties[$name] = $value;
                break;

            case 'options':
                if ( !$value instanceof ezcMailComposerOptions )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcMailComposerOptions' );
                }

                $this->options = $value;
                break;

            default:
                parent::__set( $name, $value );
        }
    }

    /**
     * Returns the property $name.
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
            case 'plainText':
            case 'htmlText':
            case 'charset':
            case 'encoding':
                return $this->properties[$name];

            case 'options':
                return $this->options;

            default:
                return parent::__get( $name );
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
            case 'plainText':
            case 'htmlText':
            case 'charset':
            case 'encoding':
                return isset( $this->properties[$name] );

            case 'options':
                return isset( $this->options );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Adds the file $fileName to the list of attachments.
     *
     * If $content is specified, $fileName is not checked if it exists.
     * $this->attachments will also contain in this case the $content,
     * $contentType and $mimeType.
     *
     * The $contentType (default = application) and $mimeType (default =
     * octet-stream) control the complete mime-type of the attachment.
     *
     * If $contentDisposition is specified, the attached file will have its
     * Content-Disposition header set according to the $contentDisposition
     * object and the filename of the attachment in the generated mail will be
     * the one from the $contentDisposition object.
     * 
     * @throws ezcBaseFileNotFoundException
     *         if $fileName does not exists.
     * @throws ezcBaseFilePermissionProblem
     *         if $fileName could not be read.
     * @param string $fileName
     * @param string $content
     * @param string $contentType
     * @param string $mimeType
     * @param ezcMailContentDispositionHeader $contentDisposition
     * @apichange This function might be removed in a future iteration of
     *            the Mail component. Use addFileAttachment() and
     *            addStringAttachment() instead.
     */
    public function addAttachment( $fileName, $content = null, $contentType = null, $mimeType = null, ezcMailContentDispositionHeader $contentDisposition = null )
    {
        if ( is_null( $content ) )
        {
            $this->addFileAttachment( $fileName, $contentType, $mimeType, $contentDisposition );
        }
        else
        {
            $this->addStringAttachment( $fileName, $content, $contentType, $mimeType, $contentDisposition );
        }
    }

    /**
     * Adds the file $fileName to the list of attachments.
     *
     * The $contentType (default = application) and $mimeType (default =
     * octet-stream) control the complete mime-type of the attachment.
     *
     * If $contentDisposition is specified, the attached file will have its
     * Content-Disposition header set according to the $contentDisposition
     * object and the filename of the attachment in the generated mail will be
     * the one from the $contentDisposition object.
     * 
     * @throws ezcBaseFileNotFoundException
     *         if $fileName does not exists.
     * @throws ezcBaseFilePermissionProblem
     *         if $fileName could not be read.
     * @param string $fileName
     * @param string $contentType
     * @param string $mimeType
     * @param ezcMailContentDispositionHeader $contentDisposition
     */
    public function addFileAttachment( $fileName, $contentType = null, $mimeType = null, ezcMailContentDispositionHeader $contentDisposition = null )
    {
        if ( is_readable( $fileName ) )
        {
            $this->attachments[] = array( $fileName, null, $contentType, $mimeType, $contentDisposition );
        }
        else
        {
            if ( file_exists( $fileName ) )
            {
                throw new ezcBaseFilePermissionException( $fileName, ezcBaseFileException::READ );
            }
            else
            {
                throw new ezcBaseFileNotFoundException( $fileName );
            }
        }
    }

    /**
     * Adds the file $fileName to the list of attachments, with contents $content.
     *
     * The file $fileName is not checked if it exists. An attachment is added
     * to the mail, with the name $fileName, and the contents $content.
     *
     * The $contentType (default = application) and $mimeType (default =
     * octet-stream) control the complete mime-type of the attachment.
     *
     * If $contentDisposition is specified, the attached file will have its
     * Content-Disposition header set according to the $contentDisposition
     * object and the filename of the attachment in the generated mail will be
     * the one from the $contentDisposition object.
     * 
     * @param string $fileName
     * @param string $content
     * @param string $contentType
     * @param string $mimeType
     * @param ezcMailContentDispositionHeader $contentDisposition
     */
    public function addStringAttachment( $fileName, $content, $contentType = null, $mimeType = null, ezcMailContentDispositionHeader $contentDisposition = null )
    {
        $this->attachments[] = array( $fileName, $content, $contentType, $mimeType, $contentDisposition );
    }

    /**
     * Builds the complete email message in RFC822 format.
     *
     * This method must be called before the message is sent.
     *
     * @throws ezcBaseFileNotFoundException
     *         if any of the attachment files can not be found.
     */
    public function build()
    {
        $mainPart = false;

        // create the text part if there is one
        if ( $this->plainText != '' )
        {
            $mainPart = new ezcMailText( $this->plainText, $this->charset );
        }

        // create the HTML part if there is one
        $htmlPart = false;
        if ( $this->htmlText != '' )
        {
            $htmlPart = $this->generateHtmlPart();

            // create a MultiPartAlternative if a text part exists
            if ( $mainPart != false )
            {
                $mainPart = new ezcMailMultipartAlternative( $mainPart, $htmlPart );
            }
            else
            {
                $mainPart = $htmlPart;
            }
        }

        // build all attachments
        // special case, mail with no text and one attachment.
        // A fix for issue #14220 was added by wrapping the attachment in
        // an ezcMailMultipartMixed part
        if ( $mainPart == false && count( $this->attachments ) == 1 )
        {
            if ( isset( $this->attachments[0][1] ) )
            {
                if ( is_resource( $this->attachments[0][1] ) )
                {
                    $mainPart = new ezcMailMultipartMixed( new ezcMailStreamFile( $this->attachments[0][0], $this->attachments[0][1], $this->attachments[0][2], $this->attachments[0][3] ) );
                }
                else
                {
                    $mainPart = new ezcMailMultipartMixed( new ezcMailVirtualFile( $this->attachments[0][0], $this->attachments[0][1], $this->attachments[0][2], $this->attachments[0][3] ) );
                }
            }
            else
            {
                $mainPart = new ezcMailMultipartMixed( new ezcMailFile( $this->attachments[0][0], $this->attachments[0][2], $this->attachments[0][3] ) );
            }
            $mainPart->contentDisposition = $this->attachments[0][4];
        }
        else if ( count( $this->attachments ) > 0 )
        {
            $mainPart = ( $mainPart == false )
                ? new ezcMailMultipartMixed()
                : new ezcMailMultipartMixed( $mainPart );

            // add the attachments to the mixed part
            foreach ( $this->attachments as $attachment )
            {
                if ( isset( $attachment[1] ) )
                {
                    if ( is_resource( $attachment[1] ) )
                    {
                        $part = new ezcMailStreamFile( $attachment[0], $attachment[1], $attachment[2], $attachment[3] );
                    }
                    else
                    {
                        $part = new ezcMailVirtualFile( $attachment[0], $attachment[1], $attachment[2], $attachment[3] );
                    }
                }
                else
                {
                    $part = new ezcMailFile( $attachment[0], $attachment[2], $attachment[3] );
                }
                $part->contentDisposition = $attachment[4];
                $mainPart->appendPart( $part );
            }
        }

        $this->body = $mainPart;
    }

    /**
     * Returns an ezcMailPart based on the HTML provided.
     *
     * This method adds local files/images to the mail itself using a
     * {@link ezcMailMultipartRelated} object.
     *
     * @throws ezcBaseFileNotFoundException
     *         if $fileName does not exists.
     * @throws ezcBaseFilePermissionProblem
     *         if $fileName could not be read.
     * @return ezcMailPart
     */
    private function generateHtmlPart()
    {
        $result = false;
        if ( $this->htmlText != '' )
        {
            $matches = array();
            if ( $this->options->automaticImageInclude === true )
            {
                // recognize file:// and file:///, pick out the image, add it as a part and then..:)
                preg_match_all( '(
                    <img \\s+[^>]*
                        src\\s*=\\s*
                            (?:
                                (?# Match quoted attribute)
                                ([\'"])file://(?P<quoted>[^>]+)\\1

                                (?# Match unquoted attribute, which may not contain spaces)
                            |   file://(?P<unquoted>[^>\\s]+)
                        )
                    [^>]* >)ix', $this->htmlText, $matches );
                // pictures/files can be added multiple times. We only need them once.
                $matches = array_filter( array_unique( array_merge( $matches['quoted'], $matches['unquoted'] ) ) );
            }

            $result = new ezcMailText( $this->htmlText, $this->charset, $this->encoding );
            $result->subType = "html";
            if ( count( $matches ) > 0 )
            {
                $htmlPart = $result;
                // wrap already existing message in an alternative part
                $result = new ezcMailMultipartRelated( $result );

                // create a filepart and add it to the related part
                // also store the ID for each part since we need those
                // when we replace the originals in the HTML message.
                foreach ( $matches as $fileName )
                {
                    if ( is_readable( $fileName ) )
                    {
                        // @todo waiting for fix of the fileinfo extension
                        // $contents = file_get_contents( $fileName );
                        $mimeType = null;
                        $contentType = null;
                        if ( ezcBaseFeatures::hasExtensionSupport( 'fileinfo' ) )
                        {
                            // if fileinfo extension is available
                            $filePart = new ezcMailFile( $fileName );
                        }
                        elseif ( ezcMailTools::guessContentType( $fileName, $contentType, $mimeType ) )
                        {
                            // if fileinfo extension is not available try to get content/mime type
                            // from the file extension
                            $filePart = new ezcMailFile( $fileName, $contentType, $mimeType );
                        }
                        else
                        {
                            // fallback in case fileinfo is not available and could not get content/mime
                            // type from file extension
                            $filePart = new ezcMailFile( $fileName, "application", "octet-stream" );
                        }
                        $cid = $result->addRelatedPart( $filePart );
                        // replace the original file reference with a reference to the cid
                        $this->htmlText = str_replace( 'file://' . $fileName, 'cid:' . $cid, $this->htmlText );
                    }
                    else
                    {
                        if ( file_exists( $fileName ) )
                        {
                            throw new ezcBaseFilePermissionException( $fileName, ezcBaseFileException::READ );
                        }
                        else
                        {
                            throw new ezcBaseFileNotFoundException( $fileName );
                        }
                        // throw
                    }
                }
                // update mail, with replaced URLs
                $htmlPart->text = $this->htmlText;
            }
        }
        return $result;
    }
}
?>
