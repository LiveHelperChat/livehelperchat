<?php
/**
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.2
 * @filesource
 * @package Translation
 */

/**
 * Translation backend that reads Qt's Linguist TS files.
 *
 * This class is the backend that can read Qt's Linguist files as source for a
 * translation. The format is an XML file, which contains contexts and all the
 * translatable strings.
 *
 * Example:
 * <code>
 * <?php
 * $a = new ezcTranslationTsBackend(
 *     'tests/translations',
 *     array ( 'format' => '[LOCALE].xml' )
 * );
 *
 * $b = new ezcTranslationManager( $a );
 * ?>
 * </code>
 *
 * The reader capabilities of this class (the implementation of
 * ezcTranslationContextRead) can be used it two different ways, where the
 * second one is the more elegant approach.
 *
 * Reader Example 1:
 * <code>
 * <?php
 * $backend = new ezcTranslationTsBackend( "files/translations" );
 * $backend->options->format = '[LOCALE].xml';
 * $backend->initReader( 'nb-no' );
 * $backend->next();
 * 
 * $contexts = array();
 * while ( $backend->valid() )
 * {
 *     $contextName = $backend->key();
 *     $contextData = $backend->current();
 *     // do something with the data
 *     $backend->next();
 * }
 * ?>
 * </code>
 *
 * Reader Example 2:
 * <code>
 * <?php
 * $backend = new ezcTranslationTsBackend( "{$currentDir}/files/translations" );
 * $backend->options->format = '[LOCALE].xml';
 * $backend->initReader( 'nb-no' );
 * 
 * $contexts = array();
 * foreach ( $backend as $contextName => $contextData )
 * {
 *     // do something with the data
 * }
 * ?>
 * </code>
 *
 * For a more extensive example see {@link ezcTranslationManager}.
 *
 * @property ezcTranslationTsBackendOptions $options
 *           Contains the options for this class.
 *
 * @package Translation
 * @version 1.3.2
 * @mainclass
 */
class ezcTranslationTsBackend implements ezcTranslationBackend, ezcTranslationContextRead, ezcTranslationContextWrite
{
    /**
     * The last read context, as read by next() method.
     *
     * The next() method is a part of the {@link ezcTranslationContextRead}
     * interface. The first element is the name, the second an array with
     * {@link ezcTranslationData} objects. An example of such an array is:
     *
     * <code>
     * array(
     *     'design/admin/class/classlist',
     *     array(
     *         new ezcTranslationData( 'Edit', 'Rediger', false, ezcTranslationData::TRANSLATED ),
     *         new ezcTranslationData( 'Create a copy of the <%class_name> class.', 'Lag en kopi av klassen <%class_name>.', false, ezcTranslationData::TRANSLATED ), 
     *     ),
     * );
     * </code>
     *
     * @var array
     */
    private $currentContext = null;

    /**
     * Handle for the XML parser used as part of the ezcTranslationContextRead interface.
     *
     * @var resource
     */
    private $xmlParser = null;

    /**
     * Contains the DOM tree for modifications while writing a new translation file
     *
     * @var DomDocument
     */
    private $dom;

    /**
     * Contains the locale used for writing a new translation file
     *
     * @var string
     */
    private $writeLocale = null;

    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties;

    /**
     * Constructs a new ezcTranslationTsBackend that will use the file specified by $location.
     *
     * You can specify additional options through the $options parameter. See
     * the documentation for the {@link ezcTranslationTsBackend::setOptions()}
     * method for supported options.
     *
     * @throws ezcTranslationNotConfiguredException if $location is not set or is empty.
     * @param string $location
     * @param array(string=>mixed) $options
     */
    function __construct( $location, array $options = array() )
    {
        if ( !$location || !strlen( $location ) )
        {
            throw new ezcTranslationNotConfiguredException( $location );
        }
        $this->properties['options'] = new ezcTranslationTsBackendOptions( $options );
        $this->properties['options']->location = $location;
    }

    /**
     * Set new options.
     * This method allows you to change the options of the translation backend.
     *  
     * @param ezcTranslationTsBackendOptions $options The options to set.
     *
     * @throws ezcBaseSettingNotFoundException
     *         If you tried to set a non-existent option value.
     * @throws ezcBaseSettingValueException
     *         If the value is not valid for the desired option.
     * @throws ezcBaseValueException
     *         If you submit neither an array nor an instance of 
     *         ezcTranslationTsBackendOptions.
     */
    public function setOptions( $options ) 
    {
        if ( is_array( $options ) ) 
        {
            $this->options->merge( $options );
        } 
        else if ( $options instanceof ezcTranslationTsBackendOptions ) 
        {
            $this->options = $options;
        }
        else
        {
            throw new ezcBaseValueException( "options", $options, "instance of ezcTranslationTsBackendOptions" );
        }
    }

    /**
     * Returns the current options.
     * Returns the options currently set for this backend.
     * 
     * @return ezcTranslationTsBackendOptions The current options.
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns the filename for the translation file using the locale $locale.
     *
     * This function uses the <i>location</i> and <i>format</i> options,
     * combined with the $locale parameter to form a filename that contains the
     * translation belonging to the specified locale.
     *
     * @param string $locale
     * @return string
     */
    public function buildTranslationFileName( $locale )
    {
        $filename = $this->options->location . $this->options->format;
        $filename = str_replace( '[LOCALE]', $locale, $filename );
        return $filename;
    }

    /**
     * Creates an SimpleXML parser object for the locale $locale.
     *
     * You can set the class of the returned object through the $returnClass
     * parameter. That class should extend the SimpleXMLElement class.
     *
     * This function checks if the <i>location</i> setting is made, if the file
     * with the filename as returned by buildTranslationFileName() exists and
     * creates a SimpleXML parser object for this file. If either the setting
     * is not made, or the file doesn't exists it throws an exception.
     *
     * @throws ezcTranslationMissingTranslationFileException if the translation
     *         could not be opened.
     * @param string $locale
     * @param string $returnClass The class of the returned XML Parser Object.
     * @return object The created parser. The parameter $returnClass
     *                 determines what type of class gets returned. The
     *                 classname that you specify should be inherited from
     *                 SimpleXMLElement.
     */
    public function openTranslationFile( $locale, $returnClass = 'SimpleXMLElement' )
    {
        $filename = $this->buildTranslationFileName( $locale );
        if ( !file_exists( $filename ) )
        {
            throw new ezcTranslationMissingTranslationFileException( $filename );
        }
        return simplexml_load_file( $filename, $returnClass );
    }

    /**
     * Creates a DOM parser object for the locale $locale.
     *
     * This function checks if the <i>location</i> setting is made, if the file
     * with the filename as returned by buildTranslationFileName() exists and
     * creates a DOM parser object for this file. If the setting
     * is not made, it throws an exception. IF the file does not exist, a new
     * DomDocument is created.
     *
     * @param string $locale
     * @return object The created parser.
     */
    public function openTranslationFileForWriting( $locale )
    {
        $filename = $this->buildTranslationFileName( $locale );
        if ( !file_exists( $filename ) )
        {
            $dom = new DOMDocument( '1.0', 'UTF-8' );
            $dom->formatOutput = true;
            $root = $dom->createElement( 'TS' );
            $dom->appendChild( $root );
        }
        else
        {
            $dom = new DOMDocument( '1.0', 'UTF-8' );
            $dom->preserveWhiteSpace = false;
            $dom->load( $filename );
            $dom->formatOutput = true;
            $root = $dom->getElementsByTagName( 'TS' )->item( 0 );
        }

        return $dom;
    }

    /**
     * Returns the data from the XML element $message as an
     * ezcTranslationData object.
     *
     * @param SimpleXMLElement $message
     * @return ezcTranslationData
     */
    private function parseSimpleXMLMessage( SimpleXMLElement $message )
    {
        $status = ezcTranslationData::TRANSLATED;
        if ( $message->translation['type'] == 'unfinished' )
        {
            $status = ezcTranslationData::UNFINISHED;
        }
        else if ( $message->translation['type'] == 'obsolete' )
        {
            if ( $this->options->keepObsolete )
            {
                $status = ezcTranslationData::OBSOLETE;
            }
            else
            {
                return null;
            }
        }

        $source = trim( (string) $message->source );
        $translation = trim( (string) $message->translation );
        $comment = trim( (string) $message->comment );

        $source = strlen( $source ) ? $source : false;
        $translation = strlen( $translation ) ? $translation : false;
        $comment = strlen( $comment ) ? $comment : false;

        $location = $message->location;
        $file = $line = false;
        if ( $location )
        {
            $file = trim( (string) $location['filename'] );
            $line = trim( (string) $location['line'] );
        }

        return new ezcTranslationData( $source, $translation, $comment, $status, $file, $line );
    }


    /**
     * Returns a array containing a translation map for the locale $locale
     * and the context $context.
     *
     * This method returns an array containing the translation map for the
     * specified $locale and $context. It uses the location and
     * format options to locate the file, unless caching is
     * enabled.
     *
     * @throws ezcTranslationContextNotAvailableException if a context is not
     *         available
     * @throws ezcTranslationMissingTranslationFileException if the translation
     *         file does not exist.
     * @throws ezcTranslationNotConfiguredException if the option <i>format</i>
     *         is not set before this method is called.
     * @param string $locale
     * @param string $context
     * @return array(ezcTranslationData)
     */
    public function getContext( $locale, $context )
    {
        $ts = $this->openTranslationFile( $locale );
        $contextElements = array();
        foreach ( $ts as $trContext )
        {
            if ( (string) $trContext->name == $context )
            {
                foreach ( $trContext as $message )
                {
                    if ( $message->source != '' )
                    {
                        $element = $this->parseSimpleXMLMessage( $message );
                        if ( is_null( $element ) )
                        {
                            continue;
                        }
                        $contextElements[] = $element;
                    }
                }
                return $contextElements;
            }
        }
        throw new ezcTranslationContextNotAvailableException( $context );
    }

    /**
     * Returns a list with all context names for the locale $locale.
     *
     * @throws ezcTranslationMissingTranslationFileException if the translation
     *         file does not exist.
     * @throws ezcTranslationNotConfiguredException if the option <i>format</i>
     *         is not set before this method is called.
     * @param string $locale
     * @return array(string)
     */
    public function getContextNames( $locale )
    {
        $ts = $this->openTranslationFile( $locale );
        $contextNames = array();
        foreach ( $ts as $trContext )
        {
            $contextNames[] = (string) $trContext->name;
        }
        return $contextNames;
    }

    /**
     * Initializes the reader to read from locale $locale.
     *
     * Opens the translation file.
     *
     * @throws ezcTranslationNotConfiguredException if the option <i>format</i>
     *         is not set before this method is called.
     *
     * @param string $locale
     * @return void
     */
    public function initReader( $locale )
    {
        $this->xmlParser = $this->openTranslationFile( $locale, 'SimpleXMLIterator' );
        $this->xmlParser->rewind();
    }

    /**
     * Deinitializes the reader
     *
     * This method should be called after the haveMore() method returns false
     * to cleanup resources.
     *
     * @throws ezcTranslationException when the reader is not initialized with
     *                                 initReader().
     * @return void
     */
    public function deinitReader()
    {
        $this->xmlParser = null;
    }

    /**
     * Stores a context.
     *
     * This method stores the context that it received to the backend specified
     * storage place.
     *
     * @throws ezcTranslationWriterNotInitializedException when the writer is
     *         not initialized with initWriter().
     * @param string $context The context's name
     * @param array(ezcTranslationData)  $data The context's translation map
     * @return void
     */
    public function storeContext( $context, array $data )
    {
        if ( is_null( $this->dom ) )
        {
            throw new ezcTranslationWriterNotInitializedException();
        }

        $dom = $this->dom;
        $root = $dom->getElementsByTagName( 'TS' )->item( 0 );

        // find the context element
        $xpath = new DOMXPath( $dom );
        $result = $xpath->query( '//context/name[text()="' . htmlspecialchars( $context ) . '"]' );

        // If the context does not exist, we create a node for it; otherwise we just use it.
        if ( !$result->length )
        {
            $contextNode = $dom->createElement( 'context' );
            $nameNode = $dom->createElement( 'name', htmlspecialchars( $context ) );
            $contextNode->appendChild( $nameNode );
            $root->appendChild( $contextNode );
        }
        else
        {
            $contextNode = $result->item( 0 )->parentNode;
        }

        // for every entry, we add a new message
        foreach ( $data as $info )
        {
            // check if the string is already there, if so, remove it first
            $xpath = new DOMXPath( $dom );
            $xpathString = str_replace( '"', '",\'"\',"', $info->original );

            $result = $xpath->query( 'message/source[text()=concat("' . $xpathString . '","")]', $contextNode );
            if ( $result->length )
            {
                $node = $result->item( 0 )->parentNode;
                $contextNode->removeChild( $node );
            }

            // create the new element
            $message = $dom->createElement( 'message' );
            $source = $dom->createElement( 'source', htmlspecialchars( $info->original ) ); 
            $message->appendChild( $source );

            $translation = $dom->createElement( 'translation', htmlspecialchars( $info->translation ) );
            switch ( $info->status )
            {
                case ezcTranslationData::UNFINISHED:
                    $translation->setAttribute( 'type', 'unfinished' );
                    break;
                case ezcTranslationData::OBSOLETE:
                    $translation->setAttribute( 'type', 'obsolete' );
                    break;
            }
            $message->appendChild( $translation );

            if ( $info->comment )
            {
                $comment = $dom->createElement( 'comment', htmlspecialchars( $info->comment ) );
                $message->appendChild( $comment );
            }

            if ( $info->filename && $info->line )
            {
                $location = $dom->createElement( 'location' );
                $location->setAttribute( 'filename', $info->filename );
                $location->setAttribute( 'line', $info->line );
                $message->appendChild( $location );
            }

            $contextNode->appendChild( $message );
        }
    }

    /**
     * Initializes the writer to write to locale $locale.
     *
     * Opens the translation file.
     *
     * @throws ezcTranslationNotConfiguredException if the option <i>format</i>
     *         is not set before this method is called.
     *
     * @param string $locale
     * @return void
     */
    public function initWriter( $locale )
    {
        $this->dom = $this->openTranslationFileForWriting( $locale );
        $this->writeLocale = $locale;
    }

    /**
     * Deinitializes the writer
     *
     * @return void
     */
    public function deinitWriter()
    {
        if ( is_null( $this->dom ) )
        {
            throw new ezcTranslationWriterNotInitializedException();
        }
        $filename = $this->buildTranslationFileName( $this->writeLocale );
        $this->dom->save( $filename );
    }

    /**
     * Advanced to the next context.
     *
     * This method parses a little bit more of the XML file to be able to
     * return the next context.  If no more contexts are available it sets the
     * $currentContext member variable to null, so that the valid() method can
     * pick this up.  If there are more contexts available it reads the context
     * from the file and stores it into the $currentContext member variable.
     * This method is used for iteration as part of the Iterator interface.
     *
     * @throws ezcTranslationReaderNotInitializedException when the reader is
     *         not initialized with initReader().
     * @return void
     */
    public function next()
    {
        if ( is_null( $this->xmlParser ) )
        {
            throw new ezcTranslationReaderNotInitializedException();
        }
        $valid = $this->xmlParser->valid();

        if ( $valid )
        {
            $newContext = array( trim( $this->xmlParser->getChildren()->name), array() );

            foreach ( $this->xmlParser->getChildren()->message as $data )
            {
                $translationItem = $this->parseSimpleXMLMessage( $data );

                if ( !is_null( $translationItem ) )
                {
                    $newContext[1][] = $translationItem;
                }
            }

            $this->currentContext = $newContext;
            $this->xmlParser->next();
        }
        else
        {
            $this->currentContext = null;
        }
    }

    /**
     * Returns whether there is a new context available.
     *
     * This method checks whether a valid context was read. It checks the
     * $currentContext member variable for the status.
     * This method is used for iteration as part of the Iterator interface.
     *
     * @throws ezcTranslationReaderNotInitializedException when the reader is
     *         not initialized with initReader().
     * @return bool
     */
    public function valid()
    {
        return $this->currentContext != null;
    }

    /**
     * Returns the current context
     *
     * This method returns the latest read context, that the haveMore() method
     * put into the $currentContext property. See
     * {@link ezcTranslationTsBackend::$currentContext} for the format of this
     * array.
     * This method is used for iteration as part of the Iterator interface.
     *
     * @throws ezcTranslationReaderNotInitializedException when the reader is
     *         not initialized with initReader().
     * @return array The current context's translation map
     */
    public function currentContext()
    {
        if ( is_null( $this->xmlParser ) )
        {
            throw new ezcTranslationReaderNotInitializedException();
        }
        return $this->currentContext;
    }

    /**
     * Returns the current context's data.
     *
     * This method returns the latest read context, that the next() method
     * put into the $currentContext property. See
     * {@link ezcTranslationTsBackend::$currentContext} for the format of this
     * array.
     * This method is used for iteration as part of the Iterator interface.
     *
     * @throws ezcTranslationReaderNotInitializedException when the reader is
     *         not initialized with initReader().
     * @return array The current context's translation map
     */
    public function current()
    {
        $context = $this->currentContext();
        return $context[1];
    }

    /**
     * Returns the current context's name.
     *
     * This method returns the latest read context, that the next() method
     * put into the $currentContext property. See
     * {@link ezcTranslationTsBackend::$currentContext} for the format of this
     * array.
     * This method is used for iteration as part of the Iterator interface.
     *
     * @throws ezcTranslationReaderNotInitializedException when the reader is
     *         not initialized with initReader().
     * @return string The current context's name
     */
    public function key()
    {
        $context = $this->currentContext();
        return $context[0];
    }

    /**
     * Empty function to satisfy the Iterator interface.
     *
     * The iterator interface expects this method to rewind to the start of
     * the array. As we do not support rewinding actually, the only thing that
     * the rewind() implementation does is reading the first element from the
     * translation file.  There are no side effects either if you just use the
     * foreach or while methods.  (See class introduction for an example).
     * This method is used for iteration as part of the Iterator interface.
     *
     * @throws ezcTranslationReaderNotInitializedException when the reader is
     *         not initialized with initReader().
     * @return void
     */
    public function rewind()
    {
        $this->next();
    }
    
    /**
     * Property read access.
     *
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     * @ignore
     */
    public function __get( $propertyName )
    {
        switch ( $propertyName ) 
        {
            case 'options':
                return $this->properties['options'];
            default:
                break;
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property write access.
     * 
     * @throws ezcBaseValueException 
     *         If a the value for the property options is not an instance of 
     *         ezcTranslationTsBackendOptions. 
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     * @ignore
     */
    public function __set( $propertyName, $val )
    {
        switch ( $propertyName ) 
        {
            case 'options':
                if ( !( $val instanceof ezcTranslationTsBackendOptions ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'instance of ezcTranslationTsBackendOptions' );
                }
                $this->properties['options'] = $val;
                return;
            default:
                break;
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }
    
    /**
     * Property isset access.
     * 
     * @param string $propertyName Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        switch ( $propertyName )
        {
            case 'options':
                return true;
        }
        return false;
    }

}
?>
