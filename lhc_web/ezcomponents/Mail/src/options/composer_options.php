<?php
/**
 * File containing the ezcMailComposerOptions class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing the options for the mail composer.
 *
 * Example of how to use the composer options:
 * <code>
 * $options = new ezcMailComposerOptions();
 * $options->automaticImageInclude = false; // default value is true
 *
 * $mail = new ezcMailComposer( $options );
 * </code>
 *
 * Alternatively, you can set the options direcly:
 * <code>
 * $mail = new ezcMailComposer();
 * $mail->options->automaticImageInclude = false;
 * </code>
 *
 * @property bool $automaticImageInclude
 *           Specifies whether to include in the generated mail the content of
 *           the files specified with "file://" in image tags. Default value
 *           is true (the contents are included).
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailComposerOptions extends ezcMailOptions
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
        $this->automaticImageInclude = true; // default is to include the contents of "file://" from image tags

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
            case 'automaticImageInclude':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'bool' );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            default:
                parent::__set( $propertyName, $propertyValue );
        }
    }
}
?>
