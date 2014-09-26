<?php
/**
 * File containing the ezcMailParser class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Parses a mail in RFC822 format to an ezcMail structure.
 *
 * By default an object of class {@link ezcMail} is returned by the parser. If
 * you want to use your own mail class (which extends {@link ezcMail}),
 * use {@link ezcMailParserOptions}. Example:
 *
 * <code>
 * $options = new ezcMailParserOptions();
 * $options->mailClass = 'myCustomMailClass'; // extends ezcMail
 *
 * $parser = new ezcMailParser( $options );
 * </code>
 *
 * Another way to do this is:
 * <code>
 * $parser = new ezcMailParser();
 * $parser->options->mailClass = 'myCustomMailClass'; // extends ezcMail
 * </code>
 *
 * File attachments will be written to disk in a temporary directory.
 * This temporary directory and the file attachment will be removed
 * when PHP ends execution. If you want to keep the file you should move it
 * to another directory.
 *
 * By default objects of class {@link ezcMailFile} are created to handle file
 * attachments. If you want to use your own file class (which extends
 * {@link ezcMailFile}), use {@link ezcMailParserOptions}. Example:
 *
 * <code>
 * $options = new ezcMailParserOptions();
 * $options->fileClass = 'myCustomFileClass'; // extends ezcMailFile
 *
 * $parser = new ezcMailParser( $options );
 * </code>
 *
 * Another way to do this is:
 * <code>
 * $parser = new ezcMailParser();
 * $parser->options->fileClass = 'myCustomFileClass'; // extends ezcMailFile
 * </code>
 *
 * By default objects of class {@link ezcMailTextPart} are created for text
 * attachments. If you want to use ezcMailFile objects instead, use
 * {@link ezcMailParserOptions}. Example:
 *
 * <code>
 * $options = new ezcMailParserOptions();
 * $options->parseTextAttachmentsAsFiles = true;
 *
 * $parser = new ezcMailParser( $options );
 * </code>
 *
 * Another way to do this is:
 * <code>
 * $parser = new ezcMailParser();
 * $parser->options->parseTextAttachmentsAsFiles = true;
 * </code>
 *
 * @property ezcMailParserOptions $options
 *           Holds the options you can set to the mail parser.
 *
 * @package Mail
 * @version 1.7.1
 * @mainclass
 */
class ezcMailParser
{
    /**
     * Holds the parser of the current mail.
     *
     * @var ezcMailPartParser
     */
    private $partParser = null;

    /**
     * Holds the directory where parsed mail should store temporary files.
     *
     * @var string
     */
    private static $tmpDir = null;

    /**
     * Holds options you can be set to the mail parser.
     *
     * @var ezcMailParserOptions
     */
    private $options;

    /**
     * Constructs a new mail parser.
     *
     * For options you can set to the mail parser see {@link ezcMailParserOptions}.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param ezcMailParserOptions|array(string=>mixed) $options
     */
    public function __construct( $options = array() )
    {
        if ( $options instanceof ezcMailParserOptions )
        {
            $this->options = $options;
        }
        else if ( is_array( $options ) )
        {
            $this->options = new ezcMailParserOptions( $options );
        }
        else
        {
            throw new ezcBaseValueException( "options", $options, "ezcMailParserOptions|array" );
        }
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not accepted for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'options':
                if ( !( $value instanceof ezcMailParserOptions ) )
                {
                    throw new ezcBaseValueException( 'options', $value, 'instanceof ezcMailParserOptions' );
                }
                $this->options = $value;
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
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'options':
                return $this->options;
            
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
            case 'options':
                return true;

            default:
                return false;
        }
    }

    /**
     * Returns an array of ezcMail objects parsed from the mail set $set.
     *
     * You can optionally use ezcMailParserOptions to provide an alternate class
     * name which will be instantiated instead of ezcMail, if you need to extend
     * ezcMail.
     *
     * Example:
     * <code>
     * $options = new ezcMailParserOptions();
     * $options->mailClass = 'MyMailClass';
     *
     * $parser = new ezcMailParser( $options );
     * // if you want to use MyMailClass which extends ezcMail
     * </code>
     *
     * @apichange Remove second parameter
     *
     * @throws ezcBaseFileNotFoundException
     *         if a neccessary temporary file could not be openened.
     * @param ezcMailParserSet $set
     * @param string $class Deprecated. Use $mailClass in ezcMailParserOptions class instead.
     * @return array(ezcMail)
     */
    public function parseMail( ezcMailParserSet $set, $class = null )
    {
        $mail = array();
        if ( !$set->hasData() )
        {
            return $mail;
        }
        if ( $class === null )
        {
            $class = $this->options->mailClass;
        }
        do
        {
            $this->partParser = new ezcMailRfc822Parser();
            $data = "";
            $size = 0;
            while ( ( $data = $set->getNextLine() ) !== null )
            {
                $this->partParser->parseBody( $data );
                $size += strlen( $data );
            }
            $part = $this->partParser->finish( $class );
            $part->size = $size;
            $mail[] = $part;
        } while ( $set->nextMail() );
        return $mail;
    }

    /**
     * Sets the temporary directory.
     *
     * The temporary directory must be writeable by PHP. It will be used to store
     * file attachments.
     *
     * @todo throw if the directory is not writeable.
     * @param string $dir
     */
    public static function setTmpDir( $dir )
    {
        self::$tmpDir = $dir;
    }

    /**
     * Returns the temporary directory.
     *
     * Uses the PHP 5.2.1 function sys_get_temp_dir().
     *
     * Note that the directory name returned will have a "slash" at the end
     * ("/" for Linux and "\" for Windows).
     *
     * @return string
     */
    public static function getTmpDir()
    {
        if ( self::$tmpDir === null )
        {
            self::$tmpDir = sys_get_temp_dir();
            if ( substr( self::$tmpDir, strlen( self::$tmpDir ) - 1 ) !== DIRECTORY_SEPARATOR )
            {
                self::$tmpDir = self::$tmpDir . DIRECTORY_SEPARATOR;
            }
        }
        return self::$tmpDir;
    }
}
?>
