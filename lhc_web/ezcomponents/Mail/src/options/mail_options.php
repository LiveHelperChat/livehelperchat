<?php
/**
 * File containing the ezcMailOptions class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the options for the mail generator.
 *
 * Example of how to use the mail options:
 * <code>
 * $options = new ezcMailOptions();
 * $options->stripBccHeader = true; // default value is false
 *
 * $mail = new ezcMail( $options );
 * </code>
 *
 * Alternatively, you can set the options direcly:
 * <code>
 * $mail = new ezcMail();
 * $mail->options->stripBccHeader = true;
 * </code>
 *
 * @property bool $stripBccHeader
 *           Specifies whether to strip the Bcc header from a mail before
 *           sending it. This can prevent problems with certain SMTP servers
 *           where the Bcc header appears visible to the To and Cc recipients
 *           (issue #16154: Bcc headers are not stripped when using SMTP).
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailOptions extends ezcBaseOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        $this->stripBccHeader = false; // default is to not strip the Bcc header

        parent::__construct( $options );
    }

    /**
     * Sets the option $propertyName to $propertyValue.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $propertyName is not defined
     * @throws ezcBaseValueException
     *         if $propertyValue is not correct for the property $propertyName
     * @param string $propertyName
     * @param mixed $propertyValue
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'stripBccHeader':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'bool' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
    }
}
?>
