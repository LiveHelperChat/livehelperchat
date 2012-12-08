<?php
/**
 * File containing the ezcMail class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The main mail class.
 *
 * You can use ezcMail together with the other classes derived from ezcMailPart
 * to build email messages. When the mail is built, use the Transport classes
 * to send the mail.
 *
 * This example builds and sends a simple text mail message:
 * <code>
 * $mail = new ezcMail;
 * $mail->from = new ezcMailAddress( 'sender@example.com', 'Adrian Ripburger' );
 * $mail->addTo( new ezcMailAddress( 'receiver@example.com', 'Maureen Corley' ) );
 * $mail->subject = "Hi";
 * $mail->body = new ezcMailText( "I just mail to say I love you!" );
 * $transport = new ezcMailMtaTransport();
 * $transport->send( $mail );
 * </code>
 *
 * By default, the ezcMail class will generate a mail with the Bcc header inside,
 * and leave it to the SMTP server to strip the Bcc header. This can pose a
 * problem with some SMTP servers which do not strip the Bcc header
 * (issue #16154: Bcc headers are not stripped when using SMTP). Use the option
 * stripBccHeader from {@link ezcMailOptions} to delete the Bcc header from
 * the mail before it is sent.
 *
 * Example:
 * <code>
 * $options = new ezcMailOptions();
 * $options->stripBccHeader = true; // default value is false
 *
 * $mail = new ezcMail( $options );
 *
 * You can also derive your own mail classes from this class if you have
 * special requirements. An example of this is the ezcMailComposer class which
 * is a convenience class to send simple mail structures and HTML mail.
 *
 * There are several headers you can set on the mail object to achieve various
 * effects:
 * - Reply-To - Set this to an email address if you want people to reply to an
 *              address other than the from address.
 * - Errors-To - If the mail can not be delivered the error message will be
 *               sent to this address.
 *
 * @property ezcMailAddress        $from Contains the from address as an
 *                                       ezcMailAddress object.
 * @property array(ezcMailAddress) $to   Contains an array of ezcMailAddress objects.
 * @property array(ezcMailAddress) $cc   Contains an array of ezcMailAddress objects.
 * @property array(ezcMailAddress) $bcc  Contains an array of ezcMailAddress objects.
 * @property string                $subject
 *                                       Contains the subject of the e-mail.
 *                                       Use setSubject if you require a
 *                                       special encoding.
 * @property string                $subjectCharset
 *                                       The encoding of the subject.
 * @property ezcMailPart           $body The body part of the message.
 *
 * @property-read string           $messageId
 *                                       The message ID of the message. Treat
 *                                       as read-only unless you're 100% sure
 *                                       what you're doing. Also accessible through
 *                                       the deprecated property messageID.
 * @property-read integer          $timestamp
 *                                       The date/time of when the message was
 *                                       sent as Unix Timestamp.
 * @property ezcMailAddress        $returnPath Contains the Return-Path address as an
 *                                             ezcMailAddress object.
 * @property ezcMailOptions $options
 *           Options for generating mail. See {@link ezcMailOptions}.
 *
 * @apichange Remove the support for the deprecated property messageID.
 *
 * @package Mail
 * @version 1.7.1
 * @mainclass
 */
class ezcMail extends ezcMailPart
{
    /**
     * 7 bit encoding.
     */
    const SEVEN_BIT = "7bit";

    /**
     * 8 bit encoding.
     */
    const EIGHT_BIT = "8bit";

    /**
     * Binary encoding.
     */
    const BINARY = "binary";

    /**
     * Quoted printable encoding.
     */
    const QUOTED_PRINTABLE = "quoted-printable";

    /**
     * Base 64 encoding.
     */
    const BASE64 = "base64";

    /**
     * Holds the options for this class.
     *
     * @var ezcMailOptions
     */
    protected $options;

    /**
     * Constructs an empty ezcMail object.
     */
    public function __construct( ezcMailOptions $options = null )
    {
        parent::__construct();

        $this->properties['from'] = null;
        $this->properties['to'] = array();
        $this->properties['cc'] = array();
        $this->properties['bcc'] = array();
        $this->properties['subject'] = null;
        $this->properties['subjectCharset'] = 'us-ascii';
        $this->properties['body'] = null;
        $this->properties['messageId'] = null;
        $this->properties['returnPath'] = null;

        if ( $options === null )
        {
            $options = new ezcMailOptions();
        }

        $this->options = $options;
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
            case 'from':
            case 'returnPath':
                if ( $value !== null && !$value instanceof ezcMailAddress )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcMailAddress or null' );
                }
                $this->properties[$name] = $value;
                break;

            case 'to':
            case 'cc':
            case 'bcc':
                if ( !is_array( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'array( ezcMailAddress )' );
                }
                foreach ( $value as $key => $obj )
                {
                    if ( !$obj instanceof ezcMailAddress )
                    {
                        throw new ezcBaseValueException( "{$name}[{$key}]", $obj, 'ezcMailAddress' );
                    }
                }
                $this->properties[$name] = $value;
                break;

            case 'subject':
                $this->properties['subject'] = trim( $value );
                break;

            case 'subjectCharset':
                $this->properties['subjectCharset'] = $value;
                break;

            case 'body':
                if ( !$value instanceof ezcMailPart )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcMailPart' );
                }
                $this->properties['body'] = $value;
                break;

            case 'messageId':
            case 'messageID':
                $this->properties['messageId'] = $value;
                break;

            case 'timestamp':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );
                break;

            default:
                parent::__set( $name, $value );
                break;
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
            case 'to':
            case 'cc':
            case 'bcc':
                return (array) $this->properties[$name];

            case 'from':
            case 'subject':
            case 'subjectCharset':
            case 'body':
            case 'messageId':
            case 'returnPath':
                return $this->properties[$name];

            case 'messageID': // deprecated version
                return $this->properties['messageId'];

            case 'timestamp':
                return strtotime( $this->getHeader( "Date" ) );

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
            case 'to':
            case 'cc':
            case 'bcc':
            case 'from':
            case 'subject':
            case 'subjectCharset':
            case 'body':
            case 'messageId':
            case 'returnPath':
                return isset( $this->properties[$name] );

            case 'messageID': // deprecated version
                return isset( $this->properties['messageId'] );

            case 'timestamp':
                return $this->getHeader( "Date" ) != null;

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Adds the ezcMailAddress $address to the list of 'to' recipients.
     *
     * @param ezcMailAddress $address
     */
    public function addTo( ezcMailAddress $address )
    {
        $this->properties['to'][] = $address;
    }

    /**
     * Adds the ezcMailAddress $address to the list of 'cc' recipients.
     *
     * @param ezcMailAddress $address
     */
    public function addCc( ezcMailAddress $address )
    {
        $this->properties['cc'][] = $address;
    }

    /**
     * Adds the ezcMailAddress $address to the list of 'bcc' recipients.
     *
     * @param ezcMailAddress $address
     */
    public function addBcc( ezcMailAddress $address )
    {
        $this->properties['bcc'][] = $address;
    }

    /**
     * Returns the generated body part of this mail.
     *
     * Returns an empty string if no body has been set.
     *
     * @return string
     */
    public function generateBody()
    {
        if ( is_subclass_of( $this->body, 'ezcMailPart' ) )
        {
            return $this->body->generateBody();
        }
        return '';
    }

    /**
     * Returns the generated headers for the mail.
     *
     * This method is called automatically when the mail message is built.
     * You can re-implement this method in subclasses if you wish to set
     * different mail headers than ezcMail.
     *
     * @return string
     */
    public function generateHeaders()
    {
        // set our headers first.
        if ( $this->from !== null )
        {
            $this->setHeader( "From", ezcMailTools::composeEmailAddress( $this->from ) );
        }

        if ( $this->to !== null )
        {
            $this->setHeader( "To", ezcMailTools::composeEmailAddresses( $this->to ) );
        }
        if ( count( $this->cc ) )
        {
            $this->setHeader( "Cc", ezcMailTools::composeEmailAddresses( $this->cc ) );
        }
        if ( count( $this->bcc ) && $this->options->stripBccHeader === false )
        {
            $this->setHeader( "Bcc", ezcMailTools::composeEmailAddresses( $this->bcc ) );
        }

        $this->setHeader( 'Subject', $this->subject, $this->subjectCharset );

        $this->setHeader( 'MIME-Version', '1.0' );
        $this->setHeader( 'User-Agent', 'eZ Components' );
        $this->setHeader( 'Date', date( 'r' ) );
        $idhost = $this->from != null && $this->from->email != '' ? $this->from->email : 'localhost';
        if ( is_null( $this->messageId ) )
        {
            $this->setHeader( 'Message-Id', '<' . ezcMailTools::generateMessageId( $idhost ) . '>' );
        }
        else
        {
            $this->setHeader( 'Message-Id', $this->messageID );
        }

        // if we have a body part, include the headers of the body
        if ( is_subclass_of( $this->body, "ezcMailPart" ) )
        {
            return parent::generateHeaders() . $this->body->generateHeaders();
        }
        return parent::generateHeaders();
    }

    /**
     * Returns an array of mail parts from the current mail.
     *
     * The array returned contains objects of classes:
     * - ezcMailText
     * - ezcMailFile
     * - ezcMailRfc822Digest
     * If the method is called with $includeDigests as true, then the returned
     * array will not contain ezcMailRfc822Digest objects, but instead the mail
     * parts inside the digests.
     * The parameter $filter can be used to restrict the returned mail parts,
     * eg. $filter = array( 'ezcMailFile' ) to return only file mail parts.
     *
     * A typical use for this function is to get a list of attachments from a mail.
     * Example:
     * <code>
     * // $mail is an ezcMail object
     * $parts = $mail->fetchParts();
     * // after the above line is executed, $parts will contain an array of mail parts objects,
     * // for example one ezcMailText object ($parts[0]) and two ezcMailRfc822Digest objects ($parts[1] and $parts[2]).
     * // the ezcMailText object will be used to render the mail text, and the
     * // other two objects will be displayed as links ("view attachment")
     *
     * // when user clicks on one of the two attachments, the parts of that attachment
     * // must be retrieved in order to render the attached digest:
     * $subparts = $parts[1]->mail->fetchParts();
     * // after the above line is executed, $subparts will contain an array of mail parts objects,
     * // for example one ezcMailText object and one ezcMailFile object
     * </code>
     *
     * @param array(string) $filter
     * @param bool $includeDigests
     * @return array(ezcMailPart)
     */
    public function fetchParts( $filter = null, $includeDigests = false )
    {
        $context = new ezcMailPartWalkContext( array( __CLASS__, 'collectPart' ) );
        $context->includeDigests = $includeDigests;
        $context->filter = $filter;
        $context->level = 0;
        $this->walkParts( $context, $this );
        return $context->getParts();
    }

    /**
     * Walks recursively through the mail parts in the specified mail object.
     *
     * $context is an object of class ezcMailPartWalkContext, which must contain
     * a valid callback function name to be applied to all mail parts. You can use
     * the collectPart() method, or create your own callback function which can
     * for example save the mail parts to disk or to a database.
     *
     * For the properties you can set to the walk context see: {@link ezcMailPartWalkContext}
     *
     * Example:
     * <code>
     * class App
     * {
     *     public static function saveMailPart( $context, $mailPart )
     *     {
     *         // code to save the $mailPart object to disk
     *     }
     * }
     *
     * // use the saveMailPart() function as a callback in walkParts()
     * // where $mail is an ezcMail object.
     * $context = new ezcMailPartWalkContext( array( 'App', 'saveMailPart' ) );
     * $context->includeDigests = true; // if you want to go through the digests in the mail
     * $mail->walkParts( $context, $mail );
     * </code>
     *
     * @param ezcMailPartWalkContext $context
     * @param ezcMailPart $mail
     */
    public function walkParts( ezcMailPartWalkContext $context, ezcMailPart $mail )
    {
        $className = get_class( $mail );
        $context->level++;
        switch ( $className )
        {
            case 'ezcMail':
            case 'ezcMailComposer':
                if ( $mail->body !== null )
                {
                    $this->walkParts( $context, $mail->body );
                }
                break;

            case 'ezcMailMultipartMixed':
            case 'ezcMailMultipartAlternative':
            case 'ezcMailMultipartDigest':
            case 'ezcMailMultipartReport':
                foreach ( $mail->getParts() as $part )
                {
                    $this->walkParts( $context, $part );
                }
                break;

            case 'ezcMailMultipartRelated':
                $this->walkParts( $context, $mail->getMainPart() );
                foreach ( $mail->getRelatedParts() as $part )
                {
                    $this->walkParts( $context, $part );
                }
                break;

            case 'ezcMailRfc822Digest':
                if ( $context->includeDigests )
                {
                    $this->walkParts( $context, $mail->mail );
                }
                elseif ( empty( $context->filter ) || in_array( $className, $context->filter ) )
                {
                    call_user_func( $context->callbackFunction, $context, $mail );
                }
                break;

            case 'ezcMailText':
            case 'ezcMailFile':
            case 'ezcMailDeliveryStatus':
                if ( empty( $context->filter ) || in_array( $className, $context->filter ) )
                {
                    call_user_func( $context->callbackFunction, $context, $mail );
                }
                break;

            default:
                // for cases where a custom mail class has been specified with $parser->options->mailClass
                if ( in_array( 'ezcMail', class_parents( $className ) ) )
                {
                    if ( $mail->body !== null )
                    {
                        $this->walkParts( $context, $mail->body );
                    }
                }

                // for cases where a custom file class has been specified with $parser->options->fileClass
                if ( in_array( 'ezcMailFile', class_parents( $className ) ) )
                {
                    if ( empty( $context->filter ) || in_array( $className, $context->filter ) )
                    {
                        call_user_func( $context->callbackFunction, $context, $mail );
                    }
                }
        }
        $context->level--;
    }

    /**
     * Saves $mail in the $context object.
     *
     * This function is used as a callback in the fetchParts() method.
     *
     * @param ezcMailPartWalkContext $context
     * @param ezcMailPart $mail
     */
    protected static function collectPart( ezcMailPartWalkContext $context, ezcMailPart $mail )
    {
        $context->appendPart( $mail );
    }
}
?>
