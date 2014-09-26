<?php
/**
 * File containing the ezcConsoleOutput class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Class for handling console output.
 *
 * The ezcConsoleOutput class provides an interface to output text to the console. It deals with formating 
 * text in different ways and offers some comfortable options to deal
 * with console text output.
 *
 * <code>
 * // Create the output handler
 * $out = new ezcConsoleOutput();
 * 
 * // Set the verbosity to level 10
 * $out->options->verbosityLevel = 10;
 * // Enable auto wrapping of lines after 40 characters
 * $out->options->autobreak    = 40;
 * 
 * // Set the color of the default output format to green
 * $out->formats->default->color   = 'green';
 * 
 * // Set the color of the output format named 'success' to white
 * $out->formats->success->color   = 'white';
 * // Set the style of the output format named 'success' to bold
 * $out->formats->success->style   = array( 'bold' );
 * 
 * // Set the color of the output format named 'failure' to red
 * $out->formats->failure->color   = 'red';
 * // Set the style of the output format named 'failure' to bold
 * $out->formats->failure->style   = array( 'bold' );
 * // Set the background color of the output format named 'failure' to blue
 * $out->formats->failure->bgcolor = 'blue';
 * 
 * // Output text with default format
 * $out->outputText( 'This is default text ' );
 * // Output text with format 'success'
 * $out->outputText( 'including success message', 'success' );
 * // Some more output with default output.
 * $out->outputText( "and a manual linebreak.\n" );
 * 
 * // Manipulate the later output
 * $out->formats->success->color = 'green';
 * $out->formats->default->color = 'blue';
 * 
 * // This is visible, since we set verbosityLevel to 10, and printed in default format (now blue)
 * $out->outputText( "Some verbose output.\n", null, 10 );
 * // This is not visible, since we set verbosityLevel to 10
 * $out->outputText( "Some more verbose output.\n", null, 20 );
 * // This is visible, since we set verbosityLevel to 10, and printed in format 'failure'
 * $out->outputText( "And some not so verbose, failure output.\n", 'failure', 5 );
 * </code>
 *
 * For a list of valid colors, style attributes and background colors, please 
 * refer to {@link ezcConsoleOutputFormat}.
 *
 * ATTENTION: Windows operating systems do not support styling of text on the
 * console. Therefore no styling sequences are generated on any version of
 * this operating system.
 * 
 * @property ezcConsoleOutputOptions $options
 *           Contains the options for this class.
 * @property ezcConsoleOutputFormats $formats
 *           Contains the output formats.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @mainclass
 */
class ezcConsoleOutput
{

    /**
     * Target to print to standard out, with output buffering possibility.
     */
    const TARGET_OUTPUT = "php://output";

    /**
     * Target to print to standard out. 
     */
    const TARGET_STDOUT = "php://stdout";

    /**
     * Target to print to standard error. 
     */
    const TARGET_STDERR = "php://stderr";

    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties;

    /**
     * Whether a position has been stored before, using the storePos() method.
     *
     * @see ezcConsoleOutput::storePos()
     * @var bool
     */
    protected $positionStored = false;

    /**
     * Stores the mapping of color names to their escape
     * sequence values.
     *
     * @var array(string=>int)
     */
    protected static $color = array(
        'gray'          => 30,
        'black'         => 30,      // Alias black to gray (Bug #8478)
        'red'           => 31,
        'green'         => 32,
        'yellow'        => 33,
        'blue'          => 34,
        'magenta'       => 35,
        'cyan'          => 36,
        'white'         => 37,
        'default'       => 39
    );

    /**
     * Stores the mapping of bgcolor names to their escape
     * sequence values.
     * 
     * @var array(string=>int)
     */
    protected static $bgcolor = array(
        'gray'       => 40,      // Alias gray to black (Bug #8478)
        'black'      => 40,
        'red'        => 41,
        'green'      => 42,
        'yellow'     => 43,
        'blue'       => 44,
        'magenta'    => 45,
        'cyan'       => 46,
        'white'      => 47,
        'default'    => 49,
    );

    /**
     * Stores the mapping of styles names to their escape
     * sequence values.
     * 
     * @var array(string=>int)
     */
    protected static $style = array( 
        'default'           => '0',
    
        'bold'              => 1,
        'faint'             => 2,
        'normal'            => 22,
        
        'italic'            => 3,
        'notitalic'         => 23,
        
        'underlined'        => 4,
        'doubleunderlined'  => 21,
        'notunderlined'     => 24,
        
        'blink'             => 5,
        'blinkfast'         => 6,
        'noblink'           => 25,
        
        'negative'          => 7,
        'positive'          => 27,
    );

    /**
     * Basic escape sequence string. Use sprintf() to insert escape codes.
     * 
     * @var string
     */
    private $escapeSequence = "\033[%sm";

    /**
     * Collection of targets to print to. 
     * 
     * @var array
     */
    private $targets = array();

    /**
     * Create a new console output handler.
     *
     * @see ezcConsoleOutput::$options
     * @see ezcConsoleOutputOptions
     * @see ezcConsoleOutput::$formats
     * @see ezcConsoleOutputFormats
     *
     * @param ezcConsoleOutputFormats $formats Formats to be used for output.
     * @param array(string=>string) $options   Options to set.
     */
    public function __construct( ezcConsoleOutputFormats $formats = null, array $options = array() )
    {
        $options = isset( $options ) ? $options : new ezcConsoleOutputOptions();
        $formats = isset( $formats ) ? $formats : new ezcConsoleOutputFormats();
        $this->properties['options'] = new ezcConsoleOutputOptions( $options );
        $this->properties['formats'] = $formats;
    }
    
    /**
     * Set new options.
     * This method allows you to change the options of an output handler.
     *  
     * @param ezcConsoleOutputOptions $options The options to set.
     *
     * @throws ezcBaseSettingNotFoundException
     *         If you tried to set a non-existent option value. 
     * @throws ezcBaseSettingValueException
     *         If the value is not valid for the desired option.
     * @throws ezcBaseValueException
     *         If you submit neither an array nor an instance of 
     *         ezcConsoleOutputOptions.
     */
    public function setOptions( $options ) 
    {
        if ( is_array( $options ) ) 
        {
            $this->properties['options']->merge( $options );
        } 
        else if ( $options instanceof ezcConsoleOutputOptions ) 
        {
            $this->properties['options'] = $options;
        }
        else
        {
            throw new ezcBaseValueException( "options", $options, "instance of ezcConsoleOutputOptions" );
        }
    }

    /**
     * Returns the current options.
     * Returns the options currently set for this output handler.
     * 
     * @return ezcConsoleOutputOptions The current options.
     */
    public function getOptions()
    {
        return $this->properties['options'];
    }

    /**
     * Property read access.
     *
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * 
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     * @ignore
     */
    public function __get( $propertyName )
    {
        switch ( $propertyName ) 
        {
            case 'options':
            case 'formats':
                return $this->properties[$propertyName];
            default:
                break;
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property write access.
     * 
     * @param string $propertyName Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBaseValueException 
     *         If a the value for the property options is not an instance of 
     *         ezcConsoleOutputOptions. 
     * @throws ezcBaseValueException 
     *         If a the value for the property formats is not an instance of 
     *         ezcConsoleOutputFormats. 
     * @ignore
     */
    public function __set( $propertyName, $val )
    {
        switch ( $propertyName ) 
        {
            case 'options':
                if ( !( $val instanceof ezcConsoleOutputOptions ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'ezcConsoleOutputOptions' );
                }
                $this->properties['options'] = $val;
                return;
            case 'formats':
                if ( !( $val instanceof ezcConsoleOutputFormats ) )
                {
                    throw new ezcBaseValueException( $propertyName, $val, 'ezcConsoleOutputFormats' );
                }
                $this->properties['formats'] = $val;
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
            case 'formats':
                return true;
        }
        return false;
    }

    /**
     * Print text to the console.
     *
     * Output a string to the console. If $format parameter is omitted, the
     * default style is chosen. Style can either be a special style {@link
     * ezcConsoleOutput::$options}, a style name {@link
     * ezcConsoleOutput::$formats} or 'default' to print with the default
     * styling.
     *
     * The $format parameter defines the name of a format. Formats are defined
     * through the $formats proprty, which contains format definitions in form
     * of {@link ezcConsoleOutputFormat} objects. The format influences the
     * outer appearance of a message (e.g. color) as well as the target the
     * message is printed to (e.g. STDERR).
     *
     * @throws ezcConsoleInvalidOutputTargetException
     *         If the given target ({@link ezcConsoleOutputFormat}) could not 
     *         be opened for writing or writing failed.
     *
     * @param string $text        The text to print.
     * @param string $format      Format chosen for printing.
     * @param int $verbosityLevel On which verbose level to output this message.
     * @return void
     */
    public function outputText( $text, $format = 'default', $verbosityLevel = 1 ) 
    {
        if ( $this->properties['options']->verbosityLevel >= $verbosityLevel ) 
        {
            if ( is_int( $this->properties['options']->autobreak ) && $this->properties['options']->autobreak > 0 )
            {
                $textLines = preg_split( "(\r\n|\n|\r)", $text );
                foreach ( $textLines as $id => $textLine )
                {
                    $textLines[$id] = wordwrap( $textLine, $this->properties['options']->autobreak, PHP_EOL, true );
                }
                $text = implode( PHP_EOL, $textLines );
            }
            // Initialize target, if not happened before
            if ( !isset( $this->targets[$this->formats->$format->target] ) )
            {
                // @ to suppress the warning. We handle error cases with an
                // exception here.
                if ( ( $this->targets[$this->formats->$format->target] = @fopen( $this->formats->$format->target, "w" ) ) === false )
                {
                    throw new ezcConsoleInvalidOutputTargetException( $this->formats->$format->target );
                }
            }
            // Print using formats or without. Note: Since the target is a part
            // of the format, it will also be ignored, if you switch formats off!
            if ( $this->properties['options']->useFormats === true )
            {
                if ( fwrite( $this->targets[$this->formats->$format->target], $this->formatText( $text, $format ) ) === false )
                {
                    throw new ezcConsoleInvalidOutputTargetException( $this->formats->$format->target );
                }
            }
            else
            {
               echo $text;
            }
        }
    }

    /**
     * Print text to the console and automatically append a line break.
     *
     * This method acts similar to {@link ezcConsoleOutput::outputText()}, in
     * fact it even uses it. The difference is, that outputLine()
     * automatically appends a manual line break to the printed text. Besides
     * that, you can leave out the $text parameter of outputLine() to only
     * print a line break.
     *
     * The $format parameter defines the name of a format. Formats are defined
     * through the $formats proprty, which contains format definitions in form
     * of {@link ezcConsoleOutputFormat} objects. The format influences the
     * outer appearance of a message (e.g. color) as well as the target the
     * message is printed to (e.g. STDERR).
     * 
     * @param string $text        The text to print.
     * @param string $format      Format chosen for printing.
     * @param int $verbosityLevel On which verbose level to output this message.
     * @return void
     */
    public function outputLine( $text = '', $format = 'default', $verbosityLevel = 1 )
    {
        $this->outputText( $text . PHP_EOL, $format, $verbosityLevel );
    }

    /**
     * Returns a formated version of the text.
     *
     * If $format parameter is omitted, the default style is chosen. The format
     * must be a valid registered format definition.  For information on the
     * formats, see {@link ezcConsoleOutput::$formats}.
     *
     * @param string $text   Text to apply style to.
     * @param string $format Format chosen to be applied.
     * @return string
     */
    public function formatText( $text, $format = 'default' ) 
    {
        switch ( ezcBaseFeatures::os() )
        {
            case "Windows":
                return $text;
            default:
                return $this->buildSequence( $format ) . $text . $this->buildSequence( 'default' );
        }
    }

    /**
     * Stores the current cursor position.
     *
     * Saves the current cursor position to return to it using 
     * {@link ezcConsoleOutput::restorePos()}. Multiple calls
     * to this method will override each other. Only the last
     * position is saved.
     *
     * @return void
     */
    public function storePos() 
    {
        if ( ezcBaseFeatures::os() !== "Windows" )
        {
            echo "\0337";
            $this->positionStored = true;
        }
    }

    /**
     * Restores a cursor position.
     *
     * Restores the cursor position last saved using {@link
     * ezcConsoleOutput::storePos()}.
     *
     * @throws ezcConsoleNoPositionStoredException 
     *         If no position is saved.
     * @return void
     */
    public function restorePos() 
    {
        if ( ezcBaseFeatures::os() !== "Windows" )
        {
            if ( $this->positionStored === false )
            {
                throw new ezcConsoleNoPositionStoredException();
            }
            echo "\0338";
        }
    }

    /**
     * Move the cursor to a specific column of the current line.
     *
     * Moves the cursor to a specific column index of the current line (default
     * is 1).
     * 
     * @param int $column Column to jump to.
     * @return void
     */
    public function toPos( $column = 1 ) 
    {
        if ( ezcBaseFeatures::os() !== "Windows" )
        {
            echo "\033[{$column}G";
        }
    }

    /**
     * Returns if a format code is valid for the specific formating option.
     *
     * This method determines if a given code is valid for a specific
     * formatting option ('color', 'bgcolor' or 'style').
     * 
     * @see ezcConsoleOutput::getFormatCode();
     *
     * @param string $type Formating type.
     * @param string $key  Format option name.
     * @return bool True if the code is valid.
     */
    public static function isValidFormatCode( $type, $key )
    {
        return isset( self::${$type}[$key] );
    }

    /**
     * Returns the escape sequence for a specific format.
     *
     * Returns the default format escape sequence, if the requested format does
     * not exist.
     * 
     * @param string $format Name of the format.
     * @return string The escape sequence.
     */
    protected function buildSequence( $format = 'default' )
    {
        if ( $format === 'default' )
        {
            return sprintf( $this->escapeSequence, 0 );
        }
        $modifiers = array();
        $formats = array( 'color', 'style', 'bgcolor' );
        foreach ( $formats as $formatType ) 
        {
            // Get modifiers
            if ( is_array( $this->formats->$format->$formatType ) )
            {
                if ( !in_array( 'default', $this->formats->$format->$formatType ) )
                {
                    foreach ( $this->formats->$format->$formatType as $singleVal ) 
                    {
                        $modifiers[] = $this->getFormatCode( $formatType, $singleVal );
                    }
                }
            }
            else
            {
                if ( $this->formats->$format->$formatType !== 'default' )
                {
                    $modifiers[] = $this->getFormatCode( $formatType, $this->formats->$format->$formatType );
                }
            }
        }
        // Merge modifiers
        return sprintf( $this->escapeSequence, implode( ';', $modifiers ) );
    }

    /**
     * Returns the code for a given formating option of a given type.
     *
     * $type is the type of formating ('color', 'bgcolor' or 'style'), $key the
     * name of the format to lookup. Returns the numeric code for the requested
     * format or 0 if format or type do not exist.
     * 
     * @see ezcConsoleOutput::isValidFormatCode()
     * 
     * @param string $type Formatting type.
     * @param string $key  Format option name.
     * @return int The code representation.
     */
    protected function getFormatCode( $type, $key )
    {
        if ( !ezcConsoleOutput::isValidFormatCode( $type, $key ) ) 
        {
            return 0;
        }
        return ezcConsoleOutput::${$type}[$key];
    }

}
?>
