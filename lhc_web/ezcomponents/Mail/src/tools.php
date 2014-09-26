<?php
/**
 * File containing the ezcMailTools class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class contains static convenience methods for composing addresses
 * and ensuring correct line-breaks in the mail.
 *
 * @package Mail
 * @version 1.7.1
 * @mainclass
 */
class ezcMailTools
{
    /**
     * Reply to sender.
     */
    const REPLY_SENDER = 1;

    /**
     * Reply to all.
     */
    const REPLY_ALL = 1;

    /**
     * Server to use for validateEmailAddressMx(). Change this if this server
     * cannot be used with your Internet Service Provider.
     *
     * Default value: 'smtp.ez.no'.
     *
     * @var string
     */
    public static $mxValidateServer = 'smtp.ez.no';

    /**
     * Email address to use for validateEmailAddressMx(). Change this if this
     * address cannot be used with your Internet Service Provider.
     *
     * Default value: 'postmaster@ez.no'.
     *
     * @var string
     */
    public static $mxValidateAddress = 'postmaster@ez.no';

    /**
     * Holds the unique ID's.
     *
     * @var int
     */
    private static $idCounter = 0;

    /**
     * The characters to use for line-breaks in the mail.
     *
     * The default is \r\n which is the value specified in RFC822.
     *
     * @var string
     */
    private static $lineBreak = "\r\n";

    /**
     * Returns ezcMailAddress $item as a RFC822 compliant address string.
     *
     * Example:
     * <code>
     * composeEmailAddress( new ezcMailAddress( 'sender@example.com', 'John Doe' ) );
     * </code>
     *
     * Returns:
     * <pre>
     * John Doe <sender@example.com>
     * </pre>
     *
     * The name part of $item will be surrounded by quotes if it contains any of
     * these characters: , @ < > : ; ' "
     *
     * @param ezcMailAddress $item
     * @return string
     */
    public static function composeEmailAddress( ezcMailAddress $item )
    {
        $name = trim( $item->name );
        if ( $name !== '' )
        {
            // remove the quotes around the name part if they are already there
            if ( $name{0} === '"' && $name{strlen( $name ) - 1} === '"' )
            {
                $name = substr( $name, 1, -1 );
            }

            // add slashes to " and \ and surround the name part with quotes
            if ( strpbrk( $name, ",@<>:;'\"" ) !== false )
            {
                $name = str_replace( '\\', '\\\\', $name );
                $name = str_replace( '"', '\"', $name );
                $name = "\"{$name}\"";
            }

            switch ( strtolower( $item->charset ) )
            {
                case 'us-ascii':
                    $text = $name . ' <' . $item->email . '>';
                    break;

                case 'iso-8859-1': case 'iso-8859-2': case 'iso-8859-3': case 'iso-8859-4':
                case 'iso-8859-5': case 'iso-8859-6': case 'iso-8859-7': case 'iso-8859-8':
                case 'iso-8859-9': case 'iso-8859-10': case 'iso-8859-11': case 'iso-8859-12':
                case 'iso-8859-13': case 'iso-8859-14': case 'iso-8859-15' :case 'iso-8859-16':
                case 'windows-1250': case 'windows-1251': case 'windows-1252':
                case 'utf-8':
                    if ( strpbrk( $name, "\x80\x81\x82\x83\x84\x85\x86\x87\x88\x89\x8a\x8b\x8c\x8d\x8e\x8f\x90\x91\x92\x93\x94\x95\x96\x97\x98\x99\x9a\x9b\x9c\x9d\x9e\x9f\xa0\xa1\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xab\xac\xad\xae\xaf\xb0\xb1\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xbb\xbc\xbd\xbe\xbf\xc0\xc1\xc2\xc3\xc4\xc5\xc6\xc7\xc8\xc9\xca\xcb\xcc\xcd\xce\xcf\xd0\xd1\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xdb\xdc\xdd\xde\xdf\xe0\xe1\xe2\xe3\xe4\xe5\xe6\xe7\xe8\xe9\xea\xeb\xec\xed\xee\xef\xf0\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa\xfb\xfc\xfd\xfe\xff" ) === false )
                    {
                        $text = $name . ' <' . $item->email . '>';
                        break;
                    }
                    // break intentionally missing

                default:
                    $preferences = array(
                        'input-charset' => $item->charset,
                        'output-charset' => $item->charset,
                        'scheme' => 'Q',
                        'line-break-chars' => ezcMailTools::lineBreak()
                    );
                    $name = iconv_mime_encode( 'dummy', $name, $preferences );
                    $name = substr( $name, 7 ); // "dummy: " + 1
                    $text = $name . ' <' . $item->email . '>';
                    break;
            }
        }
        else
        {
            $text = $item->email;
        }
        return $text;
    }

    /**
     * Returns the array $items consisting of ezcMailAddress objects
     * as one RFC822 compliant address string.
     *
     * Set foldLength to control how many characters each line can have before a line
     * break is inserted according to the folding rules specified in RFC2822.
     *
     * @param array(ezcMailAddress) $items
     * @param int $foldLength
     * @return string
     */
    public static function composeEmailAddresses( array $items, $foldLength = null )
    {
        $textElements = array();
        foreach ( $items as $item )
        {
            $textElements[] = ezcMailTools::composeEmailAddress( $item );
        }

        if ( $foldLength === null ) // quick version
        {
            return implode( ', ', $textElements );
        }

        $result = "";
        $charsSinceFold = 0;
        foreach ( $textElements as $element )
        {
            $length = strlen( $element );
            if ( ( $charsSinceFold + $length + 2 /* comma, space */ ) > $foldLength )
            {
                // fold last line if there is any
                if ( $result != '' )
                {
                    $result .= "," . ezcMailTools::lineBreak() .' ';
                    $charsSinceFold = 0;
                }
                $result .= $element;
            }
            else
            {
                if ( $result == '' )
                {
                    $result = $element;
                }
                else
                {
                    $result .= ', ' . $element;
                }
            }
            $charsSinceFold += $length + 1 /*space*/;
        }
        return $result;
    }

    /**
     * Returns an ezcMailAddress object parsed from the address string $address.
     *
     * You can set the encoding of the name part with the $encoding parameter.
     * If $encoding is omitted or set to "mime" parseEmailAddress will asume that
     * the name part is mime encoded.
     *
     * This method does not perform validation. It will also accept slightly
     * malformed addresses.
     *
     * If the mail address given can not be decoded null is returned.
     *
     * Example:
     * <code>
     * ezcMailTools::parseEmailAddress( 'John Doe <john@example.com>' );
     * </code>
     *
     * @param string $address
     * @param string $encoding
     * @return ezcMailAddress
     */
    public static function parseEmailAddress( $address, $encoding = "mime" )
    {
        // we don't care about the "group" part of the address since this is not used anywhere

        $matches = array();
        $pattern = '/<?\"?[a-zA-Z0-9!#\$\%\&\'\*\+\-\/=\?\^_`{\|}~\.]+\"?@[a-zA-Z0-9!#\$\%\&\'\*\+\-\/=\?\^_`{\|}~\.]+>?$/';
        if ( preg_match( trim( $pattern ), $address, $matches, PREG_OFFSET_CAPTURE ) != 1 )
        {
            return null;
        }
        $name = substr( $address, 0, $matches[0][1] );

        // trim <> from the address and "" from the name
        $name = trim( $name, '" ' );
        $mail = trim( $matches[0][0], '<>' );
        // remove any quotes found in mail addresses like "bah,"@example.com
        $mail = str_replace( '"', '', $mail );

        if ( $encoding == 'mime' )
        {
            // the name may contain interesting character encoding. We need to convert it.
            $name = ezcMailTools::mimeDecode( $name );
        }
        else
        {
            $name = ezcMailCharsetConverter::convertToUTF8( $name, $encoding );
        }

        $address = new ezcMailAddress( $mail, $name, 'utf-8' );
        return $address;
    }

    /**
     * Returns an array of ezcMailAddress objects parsed from the address string $addresses.
     *
     * You can set the encoding of the name parts with the $encoding parameter.
     * If $encoding is omitted or set to "mime" parseEmailAddresses will asume that
     * the name parts are mime encoded.
     *
     * Example:
     * <code>
     * ezcMailTools::parseEmailAddresses( 'John Doe <john@example.com>' );
     * </code>
     *
     * @param string $addresses
     * @param string $encoding
     * @return array(ezcMailAddress)
     */
    public static function parseEmailAddresses( $addresses, $encoding = "mime" )
    {
        $addressesArray = array();
        $inQuote = false;
        $last = 0; // last hit
        $length = strlen( $addresses );
        for ( $i = 0; $i < $length; $i++ )
        {
            if ( $addresses[$i] == '"' )
            {
                $inQuote = !$inQuote;
            }
            else if ( $addresses[$i] == ',' && !$inQuote )
            {
                $addressesArray[] = substr( $addresses, $last, $i - $last );
                $last = $i + 1; // eat comma
            }
        }

        // fetch the last one
        $addressesArray[] = substr( $addresses, $last );

        $addressObjects = array();
        foreach ( $addressesArray as $address )
        {
            $addressObject = self::parseEmailAddress( $address, $encoding );
            if ( $addressObject !== null )
            {
                $addressObjects[] = $addressObject;
            }
        }

        return $addressObjects;
    }

    /**
     * Returns true if $address is a valid email address, false otherwise.
     *
     * By default it will only validate against the same regular expression
     * used in ext/filter. It follows
     * {@link http://www.faqs.org/rfcs/rfc822.html RFC822} and
     * {@link http://www.faqs.org/rfcs/rfc2822.html RFC2822}.
     *
     * If $checkMxRecords is true, then an MX records check will be performed
     * also, by sending a test mail (RCPT TO) to $address using the MX records
     * found for the domain part of $address. MX record checking does not work
     * on Windows due to the lack of getmxrr() and checkdnsrr() PHP functions.
     * The ezcBaseFunctionalityNotSupportedException is thrown in this case.
     *
     * If checking against MX records, set these values before performing the
     * check, to ensure the MX record checks work properly:
     * <code>
     * ezcMailTools::$mxValidateServer = 'your.mail.server'; // default 'smtp.ez.no'
     * ezcMailTools::$mxValidateAddress = 'email@mail.server'; // default 'postmaster@ez.no'
     * </code>
     *
     * The input email address $address should be trimmed from white spaces
     * and/or quotes around it before calling this function (if needed).
     *
     * An email address has this form:
     * <code>
     *   localpart@domainpart
     * </code>
     *
     * The localpart has these rules, and these rules are just an approximation of
     * the rules in RFC2822:
     *  - allowed characters: . + ~ / ' - _ ` ^ $ % & ! ' | {
     *  - the dot (.) cannot be the first or the last character
     *  - the double-quote character (") can only surround the localpart (so
     *    if it appears it must be the first and the last character of localpart)
     *  - spaces are allowed if the localpart is surrounded in double-quotes
     *  - other ASCII characters (even from the extended-ASCII set) are allowed
     *    if the localparts is surrounded in double-quotes (the function
     *    ezcMailTools::composeEmailAddress will encode it when using it
     *    in a mail header)
     *  - the double-quotes character (") cannot be escaped to appear in a
     *    localpart surrounded by double quotes (so "john"doe"@example.com is not
     *    a valid email address)
     *
     * The domainpart has the same rules as a domain name, as defined in
     * {@link http://www.faqs.org/rfcs/rfc822.html RFC822} and
     * {@link http://www.faqs.org/rfcs/rfc2822.html RFC2822}.
     *
     * See also the test files (in the "Mail/tests/tools/data" directory) for
     * examples of correct and incorrect email addresses.
     *
     * @throws ezcBaseFunctionalityNotSupportedException
     *         if $checkMxRecords is true and getmxrr() or checkdnsrr() functions
     *         are missing (e.g. on Windows)
     * @param string $address
     * @param bool $checkMxRecords
     * @return bool
     */
    public static function validateEmailAddress( $address, $checkMxRecords = false )
    {
        $pattern = '/^((\"[^\"\f\n\r\t\v\b]+\")|([A-Za-z0-9_\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[A-Za-z0-9_\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]{2,}))$/';

        if ( preg_match( $pattern, $address ) )
        {
            if ( $checkMxRecords )
            {
                return self::validateEmailAddressMx( $address );
            }
            else
            {
                // $address passed through regexp, with no MX checks
                return true;
            }
        }
        else
        {
            // $address did not pass through regexp
            return false;
        }
    }

    /**
     * Checks if the email address $address is valid based on its MX records.
     *
     * Steps:
     *  - the MX records are fetched for the domain part of $address, along with
     *    their weights
     *  - the MX records are sorted based on the weights
     *  - for each MX record a connection is open
     *  - a test mail (RCPT TO) is tried to be sent to $address
     *  - if one test mail succeeds, then the address is valid, else invalid
     *
     * Set these values before calling this function, to ensure the MX record
     * checks work properly:
     * <code>
     * ezcMailTools::$mxValidateServer = 'your.mail.server'; // default 'smtp.ez.no'
     * ezcMailTools::$mxValidateAddress = 'email@mail.server'; // default 'postmaster@ez.no'
     * </code>
     *
     * MX record checking does not work on Windows due to the lack of getmxrr()
     * and checkdnsrr() PHP functions. The ezcBaseFunctionalityNotSupportedException
     * is thrown in this case.
     *
     * @throws ezcBaseFunctionalityNotSupportedException
     *         if getmxrr() or checkdnsrr() functions are missing (e.g. on Windows)
     * @param string $address
     * @return bool
     */
    protected static function validateEmailAddressMx( $address )
    {
        if ( !ezcBaseFeatures::hasFunction( 'getmxrr' ) || !ezcBaseFeatures::hasFunction( 'checkdnsrr' ) )
        {
            throw new ezcBaseFunctionalityNotSupportedException( 'Checking DNS records', 'getmxrr() or checkdnsrr() missing' );
        }

        $timeoutOpen = 3; // for fsockopen()
        $timeoutConnection = 5; // for stream_set_timeout()

        list( $local, $domain ) = explode( '@', $address );
        if ( !empty( $domain ) )
        {
            if ( getmxrr( $domain, $hosts, $weights ) )
            {
                for ( $i = 0; $i < count( $hosts ); $i++ )
                {
                    $mx[$hosts[$i]] = $weights[$i];
                }

                asort( $mx );
                $mx = array_keys( $mx );
            }
            elseif ( checkdnsrr( $domain, 'A' ) )
            {
                $mx[0] = gethostbyname( $domain );
            }
            else
            {
                $mx = array();
            }

            if ( ( $numberOfMx = count( $mx ) ) > 0 )
            {
                $smtp = array(
                               "HELO " . self::$mxValidateServer,
                               "MAIL FROM: <" . self::$mxValidateAddress . ">",
                               "RCPT TO: <{$address}>",
                               "QUIT",
                             );

                for ( $i = 0; $i < $numberOfMx; $i++ )
                {
                    if ( $socket = @fsockopen( $mx[$i], 25, $errno = 0, $errstr = 0, $timeoutOpen ) )
                    {
                        $response = fgets( $socket );
                        stream_set_timeout( $socket, $timeoutConnection );
                        $meta = stream_get_meta_data( $socket );
                        if ( !$meta['timed_out'] && !preg_match( '/^2\d\d[ -]/', $response ) )
                        {
                            return false;
                        }
                        foreach ( $smtp as $command )
                        {
                            fputs( $socket, "{$command}\r\n" );
                            $response = fgets( $socket, 4096 );
                            if ( !$meta['timed_out'] && preg_match( '/^5\d\d[ -]/', $response ) )
                            {
                                return false;
                            }
                        }
                        fclose( $socket );
                        return true;
                    }
                    elseif ( $i === $numberOfMx - 1 )
                    {
                        // none of the mail servers could be contacted
                        return false;
                    }
                }
            }
            else
            {
                // no mail servers found
                return false;
            }
        }
    }

    /**
     * Returns an unique message ID to be used for a mail message.
     *
     * The hostname $hostname will be added to the unique ID as required by RFC822.
     * If an e-mail address is provided instead, the hostname is extracted and used.
     *
     * The formula to generate the message ID is: [time_and_date].[process_id].[counter]
     *
     * @param string $hostname
     * @return string
     */
    public static function generateMessageId( $hostname )
    {
        if ( strpos( $hostname, '@' ) !== false )
        {
            $hostname = strstr( $hostname, '@' );
        }
        else
        {
            $hostname = '@' . $hostname;
        }
        return date( 'YmdGHjs' ) . '.' . getmypid() . '.' . self::$idCounter++ . $hostname;
    }

    /**
     * Returns an unique ID to be used for Content-ID headers.
     *
     * The part $partName is default set to "part". Another value can be used to provide,
     * for example, a file name of a part. $partName will be encoded with base64 to be
     * compliant with the RFCs.
     *
     * The formula used is [base64( $partName )]."@".[time].[counter]
     *
     * @param string $partName
     * @return string
     */
    public static function generateContentId( $partName = "part" )
    {
        return str_replace( array( '=', '+', '/' ), '', base64_encode( $partName ) ) . '@' .  date( 'His' ) . self::$idCounter++;
    }

    /**
     * Sets the endLine $character(s) to use when generating mail.
     * The default is to use "\r\n" as specified by RFC 2045.
     *
     * @param string $characters
     */
    public static function setLineBreak( $characters )
    {
        self::$lineBreak = $characters;
    }

    /**
     * Returns one endLine character.
     *
     * The default is to use "\n\r" as specified by RFC 2045.
     *
     * @return string
     */
    public static function lineBreak()
    {
        // Note, this function does deliberately not
        // have a $count parameter because of speed issues.
        return self::$lineBreak;
    }

    /**
     * Decodes mime encoded fields and tries to recover from errors.
     *
     * Decodes the $text encoded as a MIME string to the $charset. In case the
     * strict conversion fails this method tries to workaround the issues by
     * trying to "fix" the original $text before trying to convert it.
     *
     * @param string $text
     * @param string $charset
     * @return string
     */
    public static function mimeDecode( $text, $charset = 'utf-8' )
    {
        $origtext = $text;
        $text = @iconv_mime_decode( $text, 0, $charset );
        if ( $text !== false )
        {
            return $text;
        }

        // something went wrong while decoding, let's see if we can fix it
        // Try to fix lower case hex digits
        $text = preg_replace_callback(
            '/=(([a-f][a-f0-9])|([a-f0-9][a-f]))/',
            create_function( '$matches', 'return strtoupper($matches[0]);' ),
            $origtext
        );
        $text = @iconv_mime_decode( $text, 0, $charset );
        if ( $text !== false )
        {
            return $text;
        }

        // Workaround a bug in PHP 5.1.0-5.1.3 where the "b" and "q" methods
        // are not understood (but only "B" and "Q")
        $text = str_replace( array( '?b?', '?q?' ), array( '?B?', '?Q?' ), $origtext );
        $text = @iconv_mime_decode( $text, 0, $charset );
        if ( $text !== false )
        {
            return $text;
        }

        // Try it as latin 1 string
        $text = preg_replace( '/=\?([^?]+)\?/', '=?iso-8859-1?', $origtext );
        $text = iconv_mime_decode( $text, 0, $charset );

        return $text;
    }

    /**
     * Returns a new mail object that is a reply to the current object.
     *
     * The new mail will have the correct to, cc, bcc and reference headers set.
     * It will not have any body set.
     *
     * By default the reply will only be sent to the sender of the original mail.
     * If $type is set to REPLY_ALL, all the original recipients will be included
     * in the reply.
     *
     * Use $subjectPrefix to set the prefix to the subject of the mail. The default
     * is to prefix with 'Re: '.
     *
     * @param ezcMail $mail
     * @param ezcMailAddress $from
     * @param int $type REPLY_SENDER or REPLY_ALL
     * @param string $subjectPrefix
     * @param string $mailClass
     * @return ezcMail
     */
    static public function replyToMail( ezcMail $mail, ezcMailAddress $from,
                                        $type = self::REPLY_SENDER, $subjectPrefix = "Re: ",
                                        $mailClass = "ezcMail" )
    {
        $reply = new $mailClass();
        $reply->from = $from;

        // To = Reply-To if set
        if ( $mail->getHeader( 'Reply-To' ) != '' )
        {
            $reply->to = ezcMailTools::parseEmailAddresses( $mail->getHeader( 'Reply-To' ) );
        }
        else  // Else To = From

        {
            $reply->to = array( $mail->from );
        }

        if ( $type == self::REPLY_ALL )
        {
            // Cc = Cc + To - your own address
            $cc = array();
            foreach ( $mail->to as $address )
            {
                if ( $address->email != $from->email )
                {
                    $cc[] = $address;
                }
            }
            foreach ( $mail->cc as $address )
            {
                if ( $address->email != $from->email )
                {
                    $cc[] = $address;
                }
            }
            $reply->cc = $cc;
        }

        $reply->subject = $subjectPrefix . $mail->subject;

        if ( $mail->getHeader( 'Message-Id' ) )
        {
            // In-Reply-To = Message-Id
            $reply->setHeader( 'In-Reply-To', $mail->getHeader( 'Message-ID' ) );

            // References = References . Message-Id
            if ( $mail->getHeader( 'References' ) != '' )
            {
                $reply->setHeader( 'References', $mail->getHeader( 'References' )
                                   . ' ' . $mail->getHeader( 'Message-ID' ) );
            }
            else
            {
                $reply->setHeader( 'References', $mail->getHeader( 'Message-ID' ) );
            }
        }
        else // original mail is borked. Let's support it anyway.
        {
            $reply->setHeader( 'References', $mail->getHeader( 'References' ) );
        }

        return $reply;
    }

    /**
     * Guesses the content and mime type by using the file extension.
     *
     * The content and mime types are returned through the $contentType
     * and $mimeType arguments.
     * For the moment only for image files.
     *
     * @param string $fileName
     * @param string $contentType
     * @param string $mimeType
     */
    static public function guessContentType( $fileName, &$contentType, &$mimeType )
    {
        $extension = strtolower( pathinfo( $fileName, PATHINFO_EXTENSION ) );
        switch ( $extension )
        {
            case 'gif':
                $contentType = 'image';
                $mimeType = 'gif';
                break;

            case 'jpg':
            case 'jpe':
            case 'jpeg':
                $contentType = 'image';
                $mimeType = 'jpeg';
                break;

            case 'png':
                $contentType = 'image';
                $mimeType = 'png';
                break;

            case 'bmp':
                $contentType = 'image';
                $mimeType = 'bmp';
                break;

            case 'tif':
            case 'tiff':
                $contentType = 'image';
                $mimeType = 'tiff';
                break;

            default:
                return false;
        }
        return true;
    }

    /**
     * Replaces HTML embedded "cid:" references with replacements from $contentIdArray.
     *
     * The method matches all "cid:" references in the $htmlText and then loops
     * over each match. For each match the found content ID is looked-up as key
     * in the $contentIdArray and the value is then inserted as replacement for
     * the "cid:" reference.
     *
     * <code>
     * <?php
     * $contentIdArray = array( 'consoletools-table.png@1421450' => 'http://localhost/consoletools-table.jpg' );
     * $text = "<html> Embedded image: <img src='cid:consoletools-table.png@1421450'/> </html>";
     * $htmlBody = ezcMailTools::replaceContentIdRefs( $text, $contentIdArray );
     * // $htmlBody is now: 
     * // <html> Embedded image: <img src='http://localhost/consoletools-table.jpg'/> </html>
     * ?>
     * </code>
     *
     * The $contentIdArray can be build by iterating over all parts in the
     * mail, and for each ezcMailFilePart that you find: 1. copy the associated
     * file (fileName property of the ezcMailFilePart object) to your webroot;
     * 2. add an element to the array with the key created from the contentId
     * property from the ezcMailFilePart object. See the tutorial for an
     * example of this.
     *
     * @param string $htmlText
     * @param array(string=>string) $contentIdArray
     * @return string
     */
    static function replaceContentIdRefs( $htmlText, $contentIdArray )
    {
        preg_match_all( '@src=[\'"](cid:(.*?))[\'"]@', $htmlText, $matches );
        for ( $i = 0; $i < count( $matches[0] ); $i++ )
        {
            if ( isset( $contentIdArray[$matches[2][$i]] ) )
            {
                $htmlText = str_replace( $matches[1][$i], $contentIdArray[$matches[2][$i]], $htmlText );
            }
        }
        return $htmlText;
    }
}
?>
