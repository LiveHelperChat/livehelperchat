<?php
/**
 * File containing the ezcMailMtaTransport class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Implementation of the mail transport interface using the system MTA.
 *
 * The system MTA translates to sendmail on most Linux distributions.
 *
 * Qmail insists it should only have "\n" linebreaks and will send
 * garbled messages with the default "\r\n" setting.
 * Use ezcMailTools::setLineBreak( "\n" ) before sending mail to fix this issue.
 *
 * @package Mail
 * @version 1.7.1
 * @mainclass
 */
class ezcMailMtaTransport implements ezcMailTransport
{
    /**
     * Constructs a new ezcMailMtaTransport.
     */
    public function __construct(  )
    {
    }

    /**
     * Sends the mail $mail using the PHP mail method.
     *
     * Note that a message may not arrive at the destination even though
     * it was accepted for delivery.
     *
     * @throws ezcMailTransportException
     *         if the mail was not accepted for delivery by the MTA.
     * @param ezcMail $mail
     */
    public function send( ezcMail $mail )
    {
        $mail->appendExcludeHeaders( array( 'to', 'subject' ) );
        $headers = rtrim( $mail->generateHeaders() ); // rtrim removes the linebreak at the end, mail doesn't want it.

        if ( ( count( $mail->to ) + count( $mail->cc ) + count( $mail->bcc ) ) < 1 )
        {
            throw new ezcMailTransportException( 'No recipient addresses found in message header.' );
        }
        $additionalParameters = "";
        if ( isset( $mail->returnPath ) )
        {
            $additionalParameters = "-f{$mail->returnPath->email}";
        }
        $success = mail( ezcMailTools::composeEmailAddresses( $mail->to ),
                         $mail->getHeader( 'Subject' ), $mail->generateBody(), $headers, $additionalParameters );
        if ( $success === false )
        {
            throw new ezcMailTransportException( 'The email could not be sent by sendmail' );
        }
    }
}
?>
