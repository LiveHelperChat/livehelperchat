<?php
/**
 * File containing the ezcMailMultipartDigest class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The digest multipart type is used to bundle a list of mail objects.
 *
 * Each part will be shown in the mail in the order provided. It is not
 * necessary to bundle digested mail using a digest object. However, it is
 * considered good practice to do so when several digested mail are sent
 * together.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailMultipartDigest extends ezcMailMultipart
{
    /**
     * Constructs a new ezcMailMultipartDigest
     *
     * The constructor accepts an arbitrary number of ezcMail/ezcMailRfc822Digest objects
     * or arrays with objects of these types.
     *
     * Objects of the type ezcMail are wrapped into an ezcMailRfc822Digest object.
     *
     * Parts are added in the order provided. Parameters of the wrong
     * type are ignored.
     *
     * @param ezcMailRfc822Digest|array(ezcMailRfc822Digest) $...
     */
    public function __construct()
    {
        $args = func_get_args();
        parent::__construct( array() );
        foreach ( $args as $part )
        {
            if ( $part instanceof ezcMail  )
            {
                $this->parts[] = new ezcMailRfc822Digest( $part );
            }
            else if ( $part instanceof ezcMailRfc822Digest )
            {
                $this->parts[] = $part;
            }
            else if ( is_array( $part ) ) // add each and everyone of the parts in the array
            {
                foreach ( $part as $array_part )
                {
                    if ( $array_part instanceof ezcMail )
                    {
                        $this->parts[] = new ezcMailRfc822Digest( $array_part );
                    }
                    else if ( $array_part instanceof ezcMailRfc822Digest )
                    {
                        $this->parts[] = $array_part;
                    }
                }
            }
        }
    }

    /**
     * Appends a part to the list of parts.
     *
     * @param ezcMailRfc822Digest $part
     */
    public function appendPart( ezcMailRfc822Digest $part )
    {
        $this->parts[] = $part;
    }

    /**
     * Returns the mail parts associated with this multipart.
     *
     * @return array(ezcMail)
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Returns "digest".
     *
     * @return string
     */
    public function multipartType()
    {
        return "digest";
    }
}
?>
