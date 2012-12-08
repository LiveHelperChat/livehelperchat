<?php
/**
 * File containing the abstract ezcDocument base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * A base class for document type handlers.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocument implements ezcDocumentErrorReporting
{
    /**
     * XML document base options.
     *
     * @var ezcDocumentXmlOptions
     */
    protected $options;

    /**
     * Current document path, where the operations happen.
     *
     * @var string
     */
    protected $path = './';

    /**
     * Errors occured during the conversion process
     * 
     * @var array
     */
    protected $errors = array();

    /**
     * Construct new document
     *
     * @param ezcDocumentOptions $options
     */
    public function __construct( ezcDocumentOptions $options = null )
    {
        $this->options = ( $options === null ?
            new ezcDocumentOptions() :
            $options );
    }

    /**
     * Trigger visitor error
     *
     * Emit a vistitor error, and convert it to an exception depending on the
     * error reporting settings.
     *
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param int $position
     * @return void
     */
    public function triggerError( $level, $message, $file = null, $line = null, $position = null )
    {
        if ( $level & $this->options->errorReporting )
        {
            throw new ezcDocumentVisitException( $level, $message, $file, $line, $position );
        }
        else
        {
            // If the error should not been reported, we aggregate it to maybe
            // display it later.
            $this->errors[] = new ezcDocumentVisitException( $level, $message, $file, $line, $position );
        }
    }

    /**
     * Return list of errors occured during visiting the document.
     *
     * May be an empty array, if on errors occured, or a list of
     * ezcDocumentVisitException objects.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Create document from input string
     *
     * Create a document of the current type handler class and parse it into a
     * usable internal structure.
     *
     * @param string $string
     * @return void
     */
    abstract public function loadString( $string );

    /**
     * Create document from file
     *
     * Create a document of the current type handler class and parse it into a
     * usable internal structure. The default implementation just calls
     * loadString(), but you may want to provide an optimized implementation.
     *
     * @param string $file
     * @return void
     */
    public function loadFile( $file )
    {
        if ( !file_exists( $file ) || !is_readable( $file ) )
        {
            throw new ezcBaseFileNotFoundException( $file );
        }

        $this->path = realpath( dirname( $file ) ) . '/';
        $this->loadString(
            file_get_contents( $file )
        );
    }

    /**
     * Get document base path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set document base path
     *
     * The base path will be used as a base for relative file
     * inclusions in the document.
     * 
     * @param string $path
     */
    public function setPath( $path )
    {
        $this->path = $path;
    }

    /**
     * Return document compiled to the docbook format
     *
     * The internal document structure is compiled to the docbook format and
     * the resulting docbook document is returned.
     *
     * This method is required for all formats to have one central format, so
     * that each format can be compiled into each other format using docbook as
     * an intermediate format.
     *
     * You may of course just call an existing converter for this conversion.
     *
     * @return ezcDocumentDocbook
     */
    abstract public function getAsDocbook();

    /**
     * Create document from docbook document
     *
     * A document of the docbook format is provided and the internal document
     * structure should be created out of this.
     *
     * This method is required for all formats to have one central format, so
     * that each format can be compiled into each other format using docbook as
     * an intermediate format.
     *
     * You may of course just call an existing converter for this conversion.
     *
     * @param ezcDocumentDocbook $document
     * @return void
     */
    abstract public function createFromDocbook( ezcDocumentDocbook $document );

    /**
     * Return document as string
     *
     * Serialize the document to a string an return it.
     *
     * @return string
     */
    abstract public function save();

    /**
     * Magic wrapper for save()
     *
     * @ignore
     * @return string
     */
    public function __toString()
    {
        return $this->save();
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
        }

        throw new ezcBasePropertyNotFoundException( $name );
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
                if ( !( $value instanceof ezcDocumentOptions ) )
                {
                    throw new ezcBaseValueException( 'options', $value, 'instanceof ezcDocumentOptions' );
                }

                $this->options = $value;
                break;

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
}

?>
