<?php
/**
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.7.1
 * @filesource
 * @package Mail
 */

/**
 * A container to store a mail address in RFC822 format.
 *
 * The class ezcMailTools contains methods for transformation between several
 * formats.
 *
 * @package Mail
 * @version 1.7.1
 * @mainclass
 */
class ezcMailAddress extends ezcBaseStruct
{
    /**
     * The name of the recipient (optional).
     *
     * @var string
     */
    public $name;

    /**
     * The email address of the recipient.
     *
     * @var string
     */
    public $email;

    /**
     * The character set used in the $name property.
     *
     * The characterset defaults to us-ascii.
     */
    public $charset;

    /**
     * Constructs a new ezcMailAddress with the mail address $email and the optional name $name.
     *
     * @param string $email
     * @param string $name
     * @param string $charset
     */
    public function __construct( $email, $name = '', $charset = 'us-ascii' )
    {
        $this->name = $name;
        $this->email = $email;
        $this->charset = $charset;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcMailAddress
     */
    static public function __set_state( array $array )
    {
        return new ezcMailAddress( $array['email'], $array['name'] );
    }

    /**
     * Returns string representation of email address on string cast.
     *
     * Builds a representation in format "Name <email@example.com>", if name
     * is present, else only "<email@example.com>", if name is not present. You
     * can simply do echo with an object of type ezcMailAddress or (since PHP
     * 5.2) explicitly cast it to string using (string) $object.
     *
     * @return string String representation of the email address.
     */
    public function __toString()
    {
        return ( !empty( $this->name ) ? "{$this->name} " : "" ) . "<{$this->email}>";
    }
}
?>
