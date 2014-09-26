<?php
/**
 * File containing the ezcMailRfc822Digest class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Mail part or mail digest parts.
 *
 * This class is used to insert mail into mail.
 *
 *
 * This example assumes that the mail object to digest is availble in the $digest variable:
 * <code>
 * $mail = new ezcMail();
 * $mail->from = new ezcMailAddress( 'sender@example.com', 'Largo LaGrande' );
 * $mail->addTo( new ezcMailAddress( 'receiver@example.com', 'Wally B. Feed' ) );
 * $mail->subject = "This is the subject of the mail with a mail digest.";
 * $textPart = new ezcMailText( "This is the body of the mail with a mail digest." );
 *
 * $mail->body = new ezcMailMultipartMixed( $textPart, new ezcMailRfc822Digest( $digest ) );
 *
 * $transport = new ezcMailMtaTransport();
 * $transport->send( $mail );
 * </code>
 *
 * @property string $mail
 *           The mail object to digest.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailRfc822Digest extends ezcMailPart
{
    /**
     * Constructs a new ezcMailDigest with the mail $mail.
     *
     * @param ezcMail $mail
     */
    public function __construct( ezcMail $mail )
    {
        parent::__construct();

        $this->mail = $mail;
        $this->setHeader( 'Content-Type', 'message/rfc822' );
        $this->setHeader( 'Content-Disposition', 'inline' );
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
            case 'mail':
                $this->properties[$name] = $value;
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
            case 'mail':
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
            case 'mail':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Returns the body part of this mail consisting of the digested mail.
     *
     * @return string
     */
    public function generateBody()
    {
        return $this->mail->generate();
    }
}
?>
