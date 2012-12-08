<?php
/**
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.7.1
 * @filesource
 * @package Mail
 */

/**
 * Use this class to create a context to be passed to the walkParts() method from ezcMail.
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
 * @property array(string) $filter
 *           Used to restrict processing only to the specified mail part names.
 *           If empty or null, then ezcMailText, ezcMailFile and ezcMailRfc822Digest
 *           parts are processed. Usage e.g.: array( 'ezcMailFile' )
 * @property callback $callbackFunction
 *           Name of a function or array( 'class_name', 'function_name' )
 * @property bool $includeDigests
 *           If true then then ezcMailRfc822Digest parts are not processed by
 *           the callback function, instead the mail parts inside the digests will
 *           be available for processing.
 * @property int $level
 *           The current level in the mail part walk (0 = first level).
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailPartWalkContext
{
    /**
     * An array of mail parts (retrieved recursively from a mail object).
     *
     * @var array(ezcMailPart)
     */
    protected $parts = array();

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Constructs a new ezcMailPartWalkContext object.
     *
     * The parameter $callbackFunction must be a function name as string or as
     * array( 'class_name', 'function_name' ).
     *
     * @param callback $callbackFunction
     */
    public function __construct( $callbackFunction )
    {
        $this->callbackFunction = $callbackFunction;
        $this->level = 0;
        $this->filter = array();
        $this->includeDigests = false;
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not appropiate for property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'level':
                if ( !is_numeric( $value) || $value < 0 )
                {
                    throw new ezcBaseValueException( $name, $value, 'int >= 0' );
                }
                $this->properties[$name] = (int) $value;
                break;

            case 'includeDigests':
                if ( !is_bool( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'bool' );
                }
                $this->properties[$name] = (bool) $value;
                break;

            case 'filter':
                $this->properties[$name] = $value;
                break;

            case 'callbackFunction':
                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'level':
            case 'filter':
            case 'callbackFunction':
            case 'includeDigests':
                return $this->properties[$name];

            default:
                throw new ezcBasePropertyNotFoundException( $name );
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
            case 'level':
            case 'filter':
            case 'callbackFunction':
            case 'includeDigests':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Appends a part to the list of mail parts.
     *
     * @param ezcMailPart $part
     */
    public function appendPart( ezcMailPart $part )
    {
        $this->parts[] = $part;
    }

    /**
     * Returns the mail parts.
     *
     * @return array(ezcMailPart)
     */
    public function getParts()
    {
        return $this->parts;
    }
}
?>
