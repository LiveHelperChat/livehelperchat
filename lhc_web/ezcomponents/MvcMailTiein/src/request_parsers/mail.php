<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.0.1
 * @filesource
 * @package MvcMailTiein
 */

/**
 * Request parser that uses an e-mail message to populate an ezcMvcRequest object.
 * @package MvcMailTiein
 * @version 1.0.1
 * @mainclass
 */
class ezcMvcMailRequestParser extends ezcMvcRequestParser
{
    /**
     * Uses stdin, or the provided data in $mailMessage.
     *
     * @param string $mailMessage
     * @return ezcMvcRequest
     */
    public function createRequest( $mailMessage = null )
    {
        if ( $mailMessage === null )
        {
            $set = new ezcMailFileSet( array( "php://stdin" ) );
        }
        else
        {
            $set = new ezcMailVariableSet( $mailMessage );
        }
        $parser = new ezcMailParser();
        $mail = $parser->parseMail( $set );
        if ( count( $mail ) == 0 )
        {
            throw new ezcMvcMailNoDataException();
        }
        $mail = $mail[0];

        $this->request = new ezcMvcRequest();
        $this->processStandardHeaders( $mail );
        $this->processAcceptHeaders( $mail );
        $this->processUserAgentHeaders( $mail );
        $this->processFiles( $mail );

        $this->request->raw = $mail;

        return $this->request;
    }

    /**
     * Processes the standard headers that are not subdivided into other structs.
     *
     * @param ezcMail $mail
     */
    protected function processStandardHeaders( ezcMail $mail )
    {
        $req = $this->request;
        $req->date = isset( $mail->timestamp )
            ? new DateTime( "@{$mail->timestamp}" )
            : new DateTime();
        $req->protocol = 'mail';
        $email = $mail->to[0]->email;
        $req->host = substr( strrchr( $email, '@' ), 1 );
        $req->uri = substr( $email, 0, strrpos( $email, '@' ) );
        $req->requestId = $req->host . '/' . $req->uri;
        $req->referrer = isset( $mail->headers['In-Reply-To'] )
            ? trim( $mail->headers['In-Reply-To'], '<>' )
            : trim( substr( $mail->headers['References'], 0, strpos( $mail->headers['References'], ' ' ) -1 ), '<>' );

        // As variables we'll add the from name/address and subject
        $req->variables = array(
            'fromAddress' => $mail->from->email,
            'fromName'    => $mail->from->name,
            'subject'     => $mail->subject,
        );

        // For the body, we take the first ezcMailText part we can find. If
        // that's not enough, the rest can be accesible through raw.
        $context = new ezcMailPartWalkContext( array( $this, 'getBody' ) );
        $context->filter = array( 'ezcMailText' );
        $mail->walkParts( $context, $mail );
    }

    /**
     * Sets the request body to the text of the $mailText if the body is empty.
     *
     * @param ezcMailPartWalkContext $context
     * @param ezcMailText $mailText
     * @access private
     */
    public function getBody( ezcMailPartWalkContext $context, ezcMailText $mailText )
    {
        if ( $this->request->body == '' )
        {
            $this->request->body = $mailText->text;
        }
    }

    /**
     * Does really nothing, as Mail doesn't have those bits.
     */
    protected function processAcceptHeaders()
    {
        $this->request->accept = new ezcMvcRequestAccept;
    }

    /**
     * Processes the User Agent header into the ezcMvcRequestUserAgent struct.
     *
     * @param ezcMail $mail
     */
    protected function processUserAgentHeaders( ezcMail $mail )
    {
        $this->request->agent = new ezcMvcRequestUserAgent;
        $agent = $this->request->agent;

        $agent->agent = isset( $mail->headers['User-Agent'] )
            ? $mail->headers['User-Agent']
            : null;
    }

    /**
     * Processes file attachments.
     *
     * @param ezcMail $mail
     */
    protected function processFiles( ezcMail $mail )
    {
        $context = new ezcMailPartWalkContext( array( $this, 'addFile' ) );
        $context->filter = array( 'ezcMailFile' );
        $mail->walkParts( $context, $mail );
    }

    /**
     * Adds a found attachment to the request structure.
     *
     * @param ezcMailPartWalkContext $context
     * @param ezcMailFile $mailFile
     * @access private
     */
    public function addFile( ezcMailPartWalkContext $context, ezcMailFile $mailFile )
    {
        $file = new ezcMvcRequestFile;
        $file->mimeType = $mailFile->contentType . '/' . $mailFile->mimeType;
        $file->name = $mailFile->contentDisposition->displayFileName;
        $file->size = $mailFile->size;
        $file->status = 0;
        $file->tmpPath = $mailFile->fileName;

        $this->request->files[] = $file;
    }
}
?>
