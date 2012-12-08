<?php
/**
 * File containing the ezcMailMultipartMixed class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The mixed multipart type is used to bundle an ordered list of mail
 * parts.
 *
 * Each part will be shown in the mail in the order provided.
 *
 * The following example shows how to build a mail with a text part
 * and an attachment using ezcMailMultipartMixed.
 * <code>
 *        $mixed = new ezcMailMultipartMixed( new ezcMailTextPart( "Picture of me flying!" ),
 *                                            new ezcMailFile( "fly.jpg" ) );
 *        $mail = new ezcMail();
 *        $mail->body = $mixed;
 * </code>
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailMultipartMixed extends ezcMailMultipart
{
    /**
     * Constructs a new ezcMailMultipartMixed
     *
     * The constructor accepts an arbitrary number of ezcMailParts or arrays with ezcMailparts.
     * Parts are added in the order provided. Parameters of the wrong
     * type are ignored.
     *
     * @param ezcMailPart|array(ezcMailPart) $...
     */
    public function __construct()
    {
        $args = func_get_args();
        parent::__construct( $args );
    }

    /**
     * Appends a part to the list of parts.
     *
     * @param ezcMailPart $part
     */
    public function appendPart( ezcMailPart $part )
    {
        $this->parts[] = $part;
    }

    /**
     * Returns the mail parts associated with this multipart.
     *
     * @return array(ezcMailPart)
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Returns "mixed".
     *
     * @return string
     */
    public function multipartType()
    {
        return "mixed";
    }
}
?>
