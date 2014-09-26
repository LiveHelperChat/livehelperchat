<?php
/**
 * File containing the ezcMailText class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Mail part used for sending all forms of plain text.
 *
 * Example: ezcMailText in a plain text message
 * <code>
 * $textPart = new ezcMailText( "This is a text message" );
 * </code>
 *
 * Example: ezcMailText in a HTML message
 * <code>
 * $textPart = new ezcMailText( "<html>This is an <b>HTML</b> message"</html> );
 * $textPart->subType = 'html';
 * </code>
 *
 * @property string $charset
 *           The characterset used for this text part. Defaults to 'us-ascii'
 *           while creating mail, and is always 'utf-8' while parsing mail.
 * @property string $subType
 *           The subtype of this text part.
 *           Defaults to 'plain' for plain text.
 *           Use 'html' for HTML messages.
 * @property string $encoding
 *           The encoding of the text. Defaults to eight bit.
 * @property string $text
 *           The main data of this text part.
 * @property-read string $originalCharset
 *                The characterset in which a text part originally was before
 *                the conversion to UTF-8 when parsing incomming mail.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailText extends ezcMailPart
{
    /**
     * Constructs a new TextPart with the given $text, $charset and $encoding.
     *
     * OriginalCharset is only used when parsing mail. Parsed mail will always
     * be converted to UTF-8 in this case $originalCharset will hold the
     * charset before it was converted.
     *
     * @param string $text
     * @param string $charset
     * @param string $encoding
     * @param string $originalCharset
     */
    public function __construct( $text, $charset = "us-ascii", $encoding = ezcMail::EIGHT_BIT, $originalCharset = 'us-ascii' )
    {
        parent::__construct();

        $this->text = $text;
        $this->charset = $charset;
        $this->encoding = $encoding;
        $this->subType = 'plain';
        // We need to set this directly in the array as it's a read-only property.
        $this->properties['originalCharset'] = $originalCharset;
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist.
     * @throws ezcBasePropertyPermissionException
     *         if the property is read-only.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'charset':
            case 'subType':
            case 'encoding':
            case 'text':
                $this->properties[$name] = $value;
                break;
            case 'originalCharset':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );
                break;
            default:
                return parent::__set( $name, $value );
                break;
        }
    }

    /**
     * Sets the property $name to $value.
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
            case 'charset':
            case 'originalCharset':
            case 'subType':
            case 'encoding':
            case 'text':
                return $this->properties[$name];
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
            case 'charset':
            case 'originalCharset':
            case 'subType':
            case 'encoding':
            case 'text':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Returns the headers set for this part as a RFC822 compliant string.
     *
     * This method does not add the required two lines of space
     * to separate the headers from the body of the part.
     *
     * @see setHeader()
     * @return string
     */
    public function generateHeaders()
    {
        $this->setHeader( "Content-Type", "text/" . $this->subType . "; charset=" . $this->charset );
        $this->setHeader( "Content-Transfer-Encoding", $this->encoding );
        return parent::generateHeaders();
    }

    /**
     * Returns the generated text body of this part as a string.
     *
     * @return string
     */
    public function generateBody()
    {
        switch ( $this->encoding )
        {
            case ezcMail::BASE64:
                // leaves a \r\n to much at the end, but since it is base64 it will decode
                // properly so we just leave it
                return chunk_split( base64_encode( $this->text ), 76, ezcMailTools::lineBreak() );
                break;
            case ezcMail::QUOTED_PRINTABLE:
                 $text = preg_replace( '/[^\x21-\x3C\x3E-\x7E\x09\x20]/e',
                                       'sprintf( "=%02X", ord ( "$0" ) ) ;',  $this->text );
                 preg_match_all( '/.{1,73}([^=]{0,2})?/', $text, $match );
                 $text = implode( '=' . ezcMailTools::lineBreak(), $match[0] );
                return $text;
                break;
            default:
                return preg_replace( "/\r\n|\r|\n/", ezcMailTools::lineBreak(), $this->text );
        }
    }
}
?>
