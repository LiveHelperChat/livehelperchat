<?php
/**
 * File containing the ezcMailMultipart class.
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Abstract base class for all multipart types.
 *
 * This class provides writing functionality that is common for all multipart
 * types. Multiparts will be written to the mail in the order that they are set
 * to the $parts variable.
 *
 * @property string $boundary
 *           The boundary string to use between parts. This string is
 *           automatically generated and should only be changed for special
 *           requirements.
 * @property string $noMimeMessage
 *           Message to display to non-MIME capable email clients. The default
 *           value is stored in the constant {@link self::DEFAULT_NO_MIME_MESSAGE}.
 *
 * @package Mail
 * @version 1.7.1
 */
abstract class ezcMailMultipart extends ezcMailPart
{
    /**
     * Default message displayed to non-MIME capable email clients.
     */
    const DEFAULT_NO_MIME_MESSAGE = "This message is in MIME format. Since your mail reader does not understand\r\nthis format, some or all of this message may not be legible.";

    /**
     * An array holding the parts of this multipart.
     *
     * @var array(ezcMailPart)
     */
    protected $parts = array();

    /**
     * The counter is unique between all multipart types and is used to
     * generate unique boundary strings.
     *
     * @var int
     */
    private static $counter = 0;

    /**
     * Constructs a new ezcMailMultipart with the parts $parts.
     *
     * Subclasses typically accept an arbitrary number of parts in the
     * constructor and pass them along using func_get_args().
     *
     * $parts should be of the format array(array(ezcMailPart)|ezcMailPart)
     *
     * Subclasses must call this method in the constructor.
     * @param array $parts
     */
    public function __construct( array $parts )
    {
        parent::__construct();

        $this->noMimeMessage = self::DEFAULT_NO_MIME_MESSAGE;
        $this->boundary = $this->generateBoundary();
        $this->setHeader( "Content-Type", 'multipart/' . $this->multipartType() . '; '
                                           . 'boundary="' . $this->boundary . '"' );
        foreach ( $parts as $part )
        {
            if ( $part instanceof ezcMailPart  )
            {
                $this->parts[] = $part;
            }
            elseif ( is_array( $part ) ) // add each and everyone of the parts in the array
            {
                foreach ( $part as $array_part )
                {
                    if ( $array_part instanceof ezcMailPart )
                    {
                        $this->parts[] = $array_part;;
                    }
                }
            }
        }
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
            case 'boundary':
                $this->properties[$name] = $value;
                $this->setHeader( 'Content-Type', 'multipart/' . $this->multipartType() . '; ' .
                                  'boundary="' . $this->boundary . '"' );
                break;

            case 'noMimeMessage':
                $this->properties[$name] = $value;
                break;

            default:
                return parent::__set( $name, $value );
                break;
        }
    }

    /**
     * Returns the property $name.
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
            case 'boundary':
            case 'noMimeMessage':
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
            case 'boundary':
            case 'noMimeMessage':
                return isset( $this->properties[$name] );

            default:
                return parent::__isset( $name );
        }
    }

    /**
     * Returns the generated body for all multipart types.
     *
     * @return string
     */
    public function generateBody()
    {
        $data = $this->noMimeMessage . ezcMailTools::lineBreak();
        foreach ( $this->parts as $part )
        {
            $data .= ezcMailTools::lineBreak() . '--' . $this->boundary . ezcMailTools::lineBreak();
            $data .= $part->generate();
        }
        $data .= ezcMailTools::lineBreak() . '--' . $this->boundary . '--';
        return $data;
    }

    /**
     * Returns the type of multipart.
     *
     * @return string
     */
    abstract public function multipartType();

    /**
     * Returns a unique boundary string.
     *
     * @return string
     */
    protected static function generateBoundary()
    {
        return date( "YmdGHjs" ) . ':' . getmypid() . ':' . self::$counter++;
    }
}
?>
