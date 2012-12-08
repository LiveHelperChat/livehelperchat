<?php
/**
 * File containing the ezcPhpGenerator class
 *
 * @package PhpGenerator
 * @version 1.0.6
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcPhpGenerator provides a simple API to generate PHP code.
 *
 * This class can be used to generate quick and simple PHP scripts.  Typical
 * usage would be to generate caches in the form of arrays that also require
 * some logic.
 *
 * The following example shows how you can use ezcPhpGenerator to make a method
 * that produces a Fibonacci number.
 *
 * <code>
 * $generator = new ezcPhpGenerator( "~/file.php" );
 * $generator->appendCustomCode( 'function fibonacci( $number )' );
 * $generator->appendCustomCode( "{" );
 *
 * $generator->appendValueAssignment( "lo", 0 );
 * $generator->appendValueAssignment( "hi", 1 );
 * $generator->appendValueAssignment( "i", 2 );
 *
 * $generator->appendWhile( '$i < $number' );
 * $generator->appendCustomCode( '$hi = $lo + $hi;' );
 * $generator->appendCustomCode( '$lo = $hi - $lo;' );
 * $generator->appendCustomCode( '$i++;' );
 * $generator->appendEndWhile();
 * $generator->appendCustomCode( 'return $hi;' );
 * $generator->appendCustomCode( "}" );
 * $generator->finish();
 * </code>
 *
 * The above code will fill the file "~/file.php" with the following contents:
 * <code>
 * <?php
 * function fibonacci( $number )
 * {
 * $lo = 0;
 * $hi = 1;
 * $i = 2;
 * while ( $i < $number )
 * {
 *   $hi = $lo + $hi;
 *   $lo = $hi - $lo;
 *   $i++;
 * }
 * return $hi;
 * }
 * ?>
 * </code>
 *
 * @property int $indentLevel
 *           Contains the level of indentation. Increase or decrease by one if
 *           you want the indentation level to change.
 * @property string $lineBreak
 *           Contains the characters to use for linebreaks.  The default is
 *           "\r\n".
 * @property bool $niceIndentation
 *           Controls whether to output the PHP nicely indented or not. The
 *           default is false.
 * @property string $indentString
 *           Contains the characters that are indented per indentation level.
 *           The default is '  ' (two spaces).
 *
 * @package PhpGenerator
 * @version 1.0.6
 * @mainclass
 */
class ezcPhpGenerator
{
    /**
     * Normal assignment '='.
     */
    const ASSIGN_NORMAL = 1;

    /**
     * Text append assignment '.='.
     */
    const ASSIGN_APPEND_TEXT = 2;

    /**
     * Assignment with add '+='.
     */
    const ASSIGN_ADD = 3;

    /**
     * Assignment with subtraction '-='.
     */
    const ASSIGN_SUBTRACT = 4;

    /**
     * Assignment with array append $var[] ='.
     */
    const ASSIGN_ARRAY_APPEND = 5;


    // method control structures
    /**
     * 'if' program flow structure.
     */
    const FLOW_IF = 'if';

    /**
     * 'foreach' program flow structure.
     */
    const FLOW_FOREACH = 'foreach';

    /**
     * 'for' program flow structure.
     */
    const FLOW_FOR = 'for';

    /**
     * 'do' program flow structure.
     */
    const FLOW_DO = 'do';

    /**
     * 'while' program flow structure.
     */
    const FLOW_WHILE = 'while';


   /**
    * File resource pointing to the file specified by of tmpFilename.
    * This is used to write to during execution.
    * @var resource
    */
    private $fileResource = null;

   /**
    * ezcPhpGenerator writes to the file with this name during execution.
    * When {@link finish()} is called this file is moved to $resultFileName.
    * @var string
    */
    private $tmpFilename = null;

    /**
     * The name of the final result file set in the constructor.
     * @var string
     */
    private $resultFilename = null;

    /**
     * Whether to include < ?php and ? > to the file.
     * @var bool
     */
    private $includeStartEndTags;

    /**
     * Stack of FLOW_ constants used to check if control structures are properly nested.
     *
     * Each time a control structure (e.g appendWhile) is started the corresponding
     * FLOW_ constant is pushed on the stack. When a control structure is finalized
     * (e.g appendEndWhile) a value is popped from the stack and checked if it is of the
     * correct type.
     *
     * @var array
     */
    private $flowStack = array();

    /**
     * Holds the properties of this class.
     * @var array
     */
    private $properties = array();

    /**
     * Constructs a new ezcPhpGenerator.
     *
     * Constructs a new ezcPhpGenerator that writes to the file $fileName. If $includeStartEndTags
     * is set the start and end PHP tags will be included. It is useful to omit these if you
     * want to run the generated code using eval() later. $niceIndentation controls if the PHP output
     * should be indented correctly. This option is useful if you want to debug the generated code.
     *
     * @throws ezcBaseFileNotFoundException if $filename does not contain a valid path.
     * @throws ezcBaseFilePermissionException if the path specified by $filename is not writeable.
     * @param string $filename
     * @param bool $includeStartEndTags
     * @param bool $niceIndentation
     */
    public function __construct( $filename, $includeStartEndTags = true, $niceIndentation = false )
    {
        // properties defaults
        $this->indentLevel = 0;
        $this->lineBreak = "\r\n";
        $this->niceIndentation = $niceIndentation;
        $this->indentString = '  ';

        // other initialization
        $this->resultFilename = $filename;
        $this->includeStartEndTags = $includeStartEndTags;


        // setup file write resource
        $dir = dirname( $filename );
        if ( !file_exists( $dir ) )
        {
            throw new ezcBaseFileNotFoundException( $dir );
        }
        else if ( !is_writable( $dir ) )
        {
            throw new ezcBaseFilePermissionException( $dir, ezcBaseFileException::WRITE );
        }

        $file = basename( $filename );

        // generate a temporary name
        $id = md5( uniqid( "ezp". getmypid(), true ) );
        $this->tmpFilename = $filename . $id;

        // open the file, and make it ready for writing
        $this->fileResource = fopen( $this->tmpFilename, 'w' );
        if ( $this->fileResource == false )
        {
            $this->tmpFilename = null;
            $this->fileResource = null;
            $this->resultFilename = null;
            throw new ezcBaseFilePermissionException( $file, ezcBaseFileException::WRITE,
                                                      "Failed to open temporary file even though the directory was writable." );
        }
        if ( $this->includeStartEndTags )
        {
            $this->write( '<?php' . $this->lineBreak );
        }
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'lineBreak':
                $this->properties['lineBreak'] = $value;
                break;

            case 'indentLevel':
                $this->properties['indentLevel'] = $value;
                break;

            case 'indentString':
                $this->properties['indentString'] = $value;
                break;

            case 'niceIndentation':
                $this->properties['niceIndentation'] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
                break;
        }
    }

    /**
     * Returns the value of property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'lineBreak':
            case 'indentLevel':
            case 'indentString':
            case 'niceIndentation':
                return $this->properties[$name];

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }


    /**
     * Destructs the object.
     *
     * Removes all temporary files that are in use.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->abort();
    }

    /**
     * Completes the code generation
     *
     * This method must be called when you have finished generating a file.  It
     * moves the temporary file that was used for writing to the end result
     * file and releases used resources.
     * Subsequent calls to any methods generating code will fail.
     *
     * @throws ezcPhpGeneratorException if it was not possible to write to the
     *         output file or if there are any control structures (if/foreach
     *         etc.) still open.
     * @return void
     */
    public function finish()
    {
        $count = count( $this->flowStack );
        if ( $count != 0 )
        {
            throw new ezcPhpGeneratorFlowException( $this->flowStack[$count-1], 'finish' );
        }

        if ( $this->fileResource )
        {
            if ( $this->includeStartEndTags )
            {
                $this->write( '?'.'>' );
            }
            fclose( $this->fileResource );
            $this->fileResource = null;

            // Sigh, rename is also noisy if it can't rename
            if ( @rename( $this->tmpFilename, $this->resultFilename ) === false )
            {
                throw new ezcPhpGeneratorException( "ezcPhpGenerator could not open the file '{$this->resultFilename}' for writing." );
            }
        }
    }

    /**
     * Aborts the PHP generating. Cleans up the file handler and the temporary file.
     *
     * Subsequent calls to any methods that generate code will fail.
     *
     * @return void
     */
    public function abort()
    {
        if ( file_exists( $this->tmpFilename ) )
        {
            $this->fileResource = null;
            unlink( $this->tmpFilename );
            $this->tmpFilename = null;
        }
    }

    /**
     * Defines the variable $name with the value $value in the generated code.
     *
     * The parameter $caseSensitive determines if the defined variable is case
     * sensitive or not.  Note that $name must start with a letter or
     * underscore, followed by any number of letters, numbers, or underscores.
     * {@link http://php.net/manual/en/language.constants.php} for more information.
     * {@link http://php.net/manual/en/function.define.php}
     *
     * Example:
     * <code>
     * $php->addDefine( 'MY_CONSTANT', 5 );
     * </code>
     *
     * Produces:
     *
     * <code>
     * define( 'MY_CONSTANT', 5 );
     * </code>
     *
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the define to the output file.
     * @param string $name
     * @param string $value
     * @param bool $caseInsensitive
     * @return void
     */
    public function appendDefine( $name, $value, $caseInsensitive = false )
    {
        $valueData = var_export( $value, true );
        $case = '';
        if ( $caseInsensitive == true )
        {
            $case = ', true';
        }
        $this->write( $this->indentCode( "define( '$name', $valueData". $case . ' );' . $this->lineBreak ) );
    }

    /**
     * Assigns $value to the variable $name in the generated code.
     *
     * $value is exported using var_export(). This allows you to use complex
     * structures for $value. If you want to append an assignment to a variable
     * in the generated code use appendVariableAssignment.
     *
     * You can control the assignment type with the $assignmentType parameter.
     *
     * Example:
     * <code>
     * $array = array( 1, 2, 3 );
     * $php->appendValueAssignment( 'ProducedArray', $array );
     * </code>
     *
     * Produces the PHP code:
     * <code>
     * $ProducedArray = array( 1, 2, 3 );
     * </code>
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the assignment to the output file.
     * @param string $name
     * @param mixed $value
     * @param int $assignmentType ezcPhpGenerator:: ASSIGN_NORMAL, ASSIGN_APPEND_TEXT, ASSIGN_ADD,
     *                        ASSIGN_SUBTRACT or ASSIGN_ARRAY_APPEND.
     * @return void
     */
    public function appendValueAssignment( $name, $value, $assignmentType = ezcPhpGenerator::ASSIGN_NORMAL )
    {
        switch ( $assignmentType )
        {
            case self::ASSIGN_NORMAL:
                $this->write( $this->indentCode( "\${$name} = ". var_export( $value, true). ";{$this->lineBreak}" ) );
                break;
            case self::ASSIGN_APPEND_TEXT:
                $this->write( $this->indentCode( "\${$name} .= ". var_export( $value, true). ";{$this->lineBreak}" ) );
                break;
            case self::ASSIGN_ADD:
                $this->write( $this->indentCode( "\${$name} += ". var_export( $value, true). ";{$this->lineBreak}" ) );
                break;
            case self::ASSIGN_SUBTRACT:
                $this->write( $this->indentCode( "\${$name} -= ". var_export( $value, true). ";{$this->lineBreak}" ) );
                break;
            case self::ASSIGN_ARRAY_APPEND:
                $this->write( $this->indentCode( "\${$name}[] = ". var_export( $value, true). ";{$this->lineBreak}" ) );
                break;
            default:   // default to ASSIGN_NORMAL
                $this->write( $this->indentCode( "\${$name} = ". var_export( $value, true). ";{$this->lineBreak}" ) );
                break;
        }
    }

    /**
     * Assigns the variable named $variable to the variable $name in the
     * generated code.
     *
     * You can control the assignment type with the $assignmentType parameter.
     *
     * Example:
     * <code>
     * $php->addVariableAssignment( 'ProducedArray', 'otherVar' );
     * </code>
     *
     * Produces the PHP code:
     * <code>
     * $ProducedArray = $otherVar;
     * </code>
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the assignment to the output file.
     * @param string $name
     * @param mixed $variable
     * @param int $assignmentType ezcPhpGenerator:: ASSIGN_NORMAL, ASSIGN_APPEND_TEXT, ASSIGN_ADD,
     *                        ASSIGN_SUBTRACT or ASSIGN_ARRAY_APPEND.
     * @return void
     */
    public function appendVariableAssignment( $name, $variable, $assignmentType = ezcPhpGenerator::ASSIGN_NORMAL )
    {
        switch ( $assignmentType )
        {
            case self::ASSIGN_NORMAL:
                $this->write( $this->indentCode( "\${$name} = ". '$' . $variable. ";{$this->lineBreak}" ) );
                break;
            case self::ASSIGN_APPEND_TEXT:
                $this->write( $this->indentCode( "\${$name} .= ". '$' .$variable . ";{$this->lineBreak}" ) );
                break;
            case self::ASSIGN_ADD:
                $this->write( $this->indentCode( "\${$name} += ". '$' .$variable . ";{$this->lineBreak}" ) );
                break;
            case self::ASSIGN_SUBTRACT:
                $this->write( $this->indentCode( "\${$name} -= ". '$' .$variable . ";{$this->lineBreak}" ) );
                break;
            case self::ASSIGN_ARRAY_APPEND:
                $this->write( $this->indentCode( "\${$name}[] = ". '$' .$variable . ";{$this->lineBreak}" ) );
                break;
            default:   // default to ASSIGN_NORMAL
                $this->write( $this->indentCode( "\${$name} = ". '$' .$variable . ";{$this->lineBreak}" ) );
                break;
        }
    }


    /**
     * Unsets the variable $name in the generated code.
     *
     * Example:
     * <code>
     * $php->addVariableUnset( 'offset' );
     * </code>
     *
     * Produces the PHP code:
     * <code>
     * unset( $offset );
     * </code>
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the unset to the output file.
     * @param string $name
     * @return void
     */
    public function appendUnset( $name )
    {
        $this->write( $this->indentCode( "unset( \${$name} );{$this->lineBreak}" ) );
    }

    /**
     * Unsets the variable names in $list in the generated code.
     *
     * Example:
     * <code>
     * $php->addVariableUnsetList( array ( 'var1', 'var2' ) );
     * </code>
     *
     * Produces the PHP code:
     * <code>
     * unset( $var1, $var2 );
     * </code>

     * @see http://php.net/manual/en/function.unset.php
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the unset to the output file.
     * @param array $list Array of variable names.
     * @return void
     */
    public function appendUnsetList( array $list )
    {
        $first = true;
        $variables = '';
        foreach ( $list as $item )
        {
            if ( !$first )
            {
                $variables .= ', ';
            }
            else
            {
                $first = false;
            }
            $variables .= "\${$item}";
        }
        $this->write( $this->indentCode( "unset( $variables );{$this->lineBreak}" ) );
    }

    /**
     * Inserts $lines number of empty lines in the generated code.
     *
     * Example:
     * <code>
     * $php->addSpace( 1 );
     * </code>
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the empty lines to the output file.
     * @param int $lines
     * @return void
     */
    public function appendEmptyLines( $lines = 1 )
    {
        $this->write( str_repeat( $this->lineBreak, $lines ) );
    }

    /**
     * Inserts a function call in the generated code.
     *
     * Inserts a call to $functionName with the parameters $parameters.
     * Set the $returnData parameter if you want to catch the return value.
     *
     * Example:
     * <code>
     * $php->appendFunctionCall( 'str_repeat', array( new ezcPhpGeneratorParameter( 'repeat' ),
     *                                                new ezcPhpGeneratorParameter( 4, ezcPhpGenerator::VALUE ) );
     * </code>
     *
     * Produces the PHP code:
     * <code>
     * $var = str_repeat( $repeat, 4 );
     * </code>
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the method call to the output file.
     * @param string $functionName
     * @param array(ezcPhpGeneratorParameter) $parameters
     * @param ezcPhpGeneratorReturnData $returnData
     * @return void
     */
    public function appendFunctionCall( $functionName, array $parameters, ezcPhpGeneratorReturnData $returnData = null )
    {
        $this->appendMethodOrFunctionCall( $functionName, $parameters, $returnData );
    }

    /**
     * Inserts a method call on an object in the generated code.
     *
     * Inserts a call to the method $methodName on the object $objectName with
     * parameters $parameters.
     * Set the $returnData parameter if you want to catch the return value.
     *
     * Example:
     * <code>
     * $php->appendMethodCall( 'node', 'name', array(), new ezcPhpGeneratorReturnType( 'result' ) );
     * </code>
     *
     * Produces the PHP code:
     *
     * <code>
     * $result = $node->name();
     * </code>
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the method call to the output file.
     * @param string $objectName
     * @param string $methodName
     * @param array(ezcPhpGeneratorParameter) $parameters
     * @param ezcPhpGeneratorReturnData $returnData
     * @return void
     */
    public function appendMethodCall( $objectName, $methodName, array $parameters = array(), ezcPhpGeneratorReturnData $returnData = null )
    {
        $this->appendMethodOrFunctionCall( $methodName, $parameters, $returnData, $objectName );
    }

    /**
     * Inserts a method or function call in the generated code.
     *
     * A method call is inserted if $objectName is provided. If not a function call is inserted. This
     * method is a helper method for appendFunctionCall and appendMethodCall. See their description for
     * further description of the parameters and examples.
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the method call to the output file.
     * @param string $functionName
     * @param array(ezcPhpGeneratorParameter) $parameters
     * @param ezcPhpGeneratorReturnData $returnData
     * @param string $objectVariable The variable name containing the object.
     * @return void
     */
    protected function appendMethodOrFunctionCall( $functionName, array $parameters, $returnData = null,
                                                   $objectVariable = null )
    {
        // prepare the return part
        $returnString = '';
        if ( $returnData != null )
        {
            switch ( $returnData->type )
            {
                case self::ASSIGN_NORMAL:
                    $returnString = "\${$returnData->variable} =";
                    break;
                case self::ASSIGN_APPEND_TEXT:
                    $returnString = "\${$returnData->variable} .=";
                    break;
                case self::ASSIGN_ADD:
                    $returnString = "\${$returnData->variable} +=";
                    break;
                case self::ASSIGN_SUBTRACT:
                    $returnString = "\${$returnData->variable} -=";
                    break;
                case self::ASSIGN_ARRAY_APPEND:
                    $returnString = "\${$returnData->variable}[] =";
                    break;
                default:   // default to ASSIGN_NORMAL
                    $returnString = "\${$returnData->variable} =";
                    break;
            }
            $returnString .= ' '; // append trailing space
        }

        // prepare the object string if this is a call to an object
        $objectString = '';
        if ( $objectVariable !== null )
        {
            $objectString = "\${$objectVariable}->";
        }

        // prepare the parameters
        $parameterString = '';
        if ( is_array( $parameters ) && count( $parameters ) > 0 )
        {
            $firstParam = true;
            foreach ( $parameters as $parameter )
            {
                if ( $parameter->type == ezcPhpGeneratorParameter::VALUE )
                {
                    $parameterString .= $firstParam ? '' : ', ';
                    $parameterString .= var_export( $parameter->variable, true );
                }
                else if ( $parameter->type == ezcPhpGeneratorParameter::VARIABLE )
                {
                    $parameterString .= $firstParam ? '' : ', ';
                    $parameterString .= "\${$parameter->variable}";
                }
                // else <-- we could have thrown an exception, but we simply ignore this
                $firstParam = false;
            }
        }
        $this->write( $this->indentCode( "$returnString$objectString$functionName( $parameterString );" . $this->lineBreak ) );
    }

    /**
     * Inserts custom code into the generated code.
     *
     * Inserts the $code directly into the generated code.  Correct indenting
     * and a linebreak at the end of your code will be inserted automatically.
     *
     * Example:
     * <code>
     * $php->addCodePiece( "if ( \$value > 2 )" . $php->lineBreak()
     *                     . '{' . $php->lineBreak() .  "\$value = 2;" );
     * </code>
     *
     * Produces the PHP code:
     * <code>
     * if ( $value > 2 )
     * {
     *   $value = 2;
     * }
     * </code>
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the custom code to the output file.
     * @param string $code
     * @return void
     */
    public function appendCustomCode( $code )
    {
        $this->write( $this->indentCode( $code . $this->lineBreak ) );
    }

    /**
     * Inserts a comment into the generated code.
     *
     * The comment will be displayed using an end-of-line comment (//).
     *
     * Example:
     * <code>
     * $php->addComment( "This file is auto generated. Do not edit!" );
     * <code>
     *
     * Produces the PHP code:
     * <code>
     * // This file is auto generated. Do not edit!
     * </code>
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the comment to the output file.
     * @param string $comment
     * @return void
     */
    public function appendComment( $comment )
    {
        $this->write( $this->indentCode( '// ' . $comment . $this->lineBreak ) );
    }

    /**
     * Inserts an if statement into the generated code.
     *
     * The complete condition of the if statement is provided through $condition.
     * The if statement must be closed properly with a call to appendEndIf().
     *
     * Example:
     * <code>
     * $php->appendIf( '$myVar === 0 ' );
     * $php->appendEndIf();
     * </code>
     *
     * Produces the PHP code:
     *
     * <code>
     * if ( $myVar === 0 )
     * {
     * }
     * </code>
     *
     * @see $ezcPhpGenerator::appendElse()
     * @see $ezcPhpGenerator::appendEndIf()
     * @throws ezcPhpGeneratorException if it was not possible to write the if statement to the output file.
     * @param string $condition
     * @return void
     */
    public function appendIf( $condition )
    {
        $this->write( $this->indentCode( "if ( $condition )" . $this->lineBreak . '{' . $this->lineBreak ) );
        $this->indentLevel++;
        $this->flowStack[] = self::FLOW_IF;
    }

    /**
     * Ends an if statement in the generated code.
     *
     * @see $ezcPhpGenerator::appendIf()
     *
     * @throws ezcPhpGeneratorException if it was not possible to write to the output file
     *         or if the method was not properly nested with an appendIf.
     * @return void
     */
    public function appendEndIf()
    {
        $this->appendEnd( self::FLOW_IF );
    }

    /**
     * Inserts an else or an else if statement into the generated code.
     *
     * If a $condition is provided an else if statement is generated.
     * If not, an else statement is generated. You can only call this method
     * after calling appendIf first.
     *
     * Example:
     * <code>
     * $php->appendIf( '$myVar === 0 ' );
     * $php->appendElse( '$myVar2 === 0 ' );
     * $php->appendElse();
     * $php->appendEndIf();
     * </code>
     *
     * Produces the PHP code:
     * <code>
     * if ( $myVar === 0 )
     * {
     * }
     * else if ( $myVar )
     * {
     * }
     * else
     * {
     * }
     * </code>
     *
     * @see $ezcPhpGenerator::appendIf()
     * @see $ezcPhpGenerator::appendEndIf()
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the if statement to the output file.
     * @param string $condition
     * @return void
     */
    public function appendElse( $condition = '' )
    {
        // check that we are in the correct flow type
        $pop = array_pop( $this->flowStack );
        if ( $pop == self::FLOW_IF )
        {
            $this->flowStack[] = self::FLOW_IF; // push it back
            $this->indentLevel--;
            if ( $condition != '' )
            {
                $condition = 'if ( ' . $condition . ' )';
            }
            $this->write( $this->indentCode( '}' . $this->lineBreak ) . "else $condition" .
                                             $this->lineBreak . '{' . $this->lineBreak );
            $this->indentLevel++;
        }
        else
        {
            $this->abort();
            $current = $pop ? $pop : 'no control structure';
            throw new ezcPhpGeneratorFlowException( $current, 'else' );
        }
    }

    /**
     * Inserts a foreach statement into the generated code.
     *
     * The complete condition of the foreach statement is provided through $condition.
     * The foreach statement must be closed properly with a call to appendEndForeach().
     *
     * Example:
     * <code>
     * $php->appendForeach( '$myArray as $item ' );
     * $php->appendEndForeach();
     * </code>
     *
     * Produces the PHP code:
     * <code>
     * foreach ( $myArray as $item )
     * {
     * }
     * </code>
     *
     * @see $ezcPhpGenerator::appendEndForeach()
     * @throws ezcPhpGeneratorException if it was not possible to write the foreach statement to the output file.
     * @param string $condition
     * @return void
     */
    public function appendForeach( $condition )
    {
        $this->write( $this->indentCode( "foreach ( $condition )" . $this->lineBreak . '{' . $this->lineBreak ) );
        $this->indentLevel++;
        $this->flowStack[] = self::FLOW_FOREACH;
    }

    /**
     * Ends a foreach statement in the generated code.
     *
     * @see $ezcPhpGenerator::appendForeach()
     *
     * @throws ezcPhpGeneratorException if it was not possible to write to the output file
     *         or if the method was not properly nested with an appendForeach.
     * @return void
     */
    public function appendEndForeach()
    {
        $this->appendEnd( self::FLOW_FOREACH );
    }

    /**
     * Inserts a while statement in the generated code.
     *
     * The complete condition of the while statement is provided through $condition.
     * The while statement must be closed properly with a call to appendEndWhile().
     *
     * Example:
     * <code>
     * $php->appendWhile( '$myVar > 0' );
     * $php->appendEndWhile();
     * </code>
     *
     * Produces the PHP code:
     * <code>
     * while ( $myVar > 0 )
     * {
     * }
     * </code>
     *
     * @see $ezcPhpGenerator::appendEndWhile()
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the while statement to the output file.
     * @param string $condition
     * @return void
     */
    public function appendWhile( $condition )
    {
        $this->write( $this->indentCode( "while ( $condition )" . $this->lineBreak . '{' . $this->lineBreak ) );
        $this->indentLevel++;
        $this->flowStack[] = self::FLOW_WHILE;
    }

    /**
     * Ends a while statement in the generated code.
     *
     * @see $ezcPhpGenerator::appendWhile()
     *
     * @throws ezcPhpGeneratorException if it was not possible to write to the output file
     *         or if the method was not properly nested with an appendWhile.
     * @return void
     */
    public function appendEndWhile()
    {
        $this->appendEnd( self::FLOW_WHILE );
    }

    /**
     * Inserts a do statement in the generated code.
     *
     * The do statement must be closed properly with a call to appendEndDo().
     *
     * Example:
     * <code>
     * $php->appendDo();
     * $php->appendEndDo( '$myVar > 0' );
     * </code>
     *
     * Produces the PHP code:
     * <code>
     * do
     * {
     * } while ( $myVar > 0 );
     * </code>
     *
     * @see $ezcPhpGenerator::appendEndDo()
     *
     * @throws ezcPhpGeneratorException if it was not possible to write to the output file.
     * @return void
     */
    public function appendDo()
    {
        $this->write( $this->indentCode( 'do' . $this->lineBreak . '{' . $this->lineBreak ) );
        $this->indentLevel++;
        $this->flowStack[] = self::FLOW_DO;
    }

    /**
     * Ends a do statement in the generated code.
     *
     * The complete condition of the do statement is provided through $condition.
     *
     * @see $ezcPhpGenerator::appendDo()
     *
     * @throws ezcPhpGeneratorException if it was not possible to write the do statement to the output file
     *         or if the method was not properly nested with an appendDo.
     * @param string $condition
     * @return void
     */
    public function appendEndDo( $condition )
    {
        $pop = array_pop( $this->flowStack );
        if ( $pop == self::FLOW_DO )
        {
            $this->indentLevel--;
            $this->write( $this->indentCode( '} while ( ' . $condition . ' );'. $this->lineBreak ) );
        }
        else
        {
            $this->abort();
            throw new ezcPhpGeneratorFlowException( $pop, 'do' );
        }
    }

    /**
     * Checks that the end call is properly nested using $type and the flow stack.
     *
     * @throws ezcPhpGeneratorException if it was not possible to write to the output file or if a nesting
     *         error was detected.
     * @param int $type One of the flow types FLOW_IF, FLOW_FOREACH, FLOW_WHILE or FLOW_DO.
     * @return void
     */
    private function appendEnd( $type )
    {
        $pop = array_pop( $this->flowStack );
        if ( $pop == $type )
        {
            $this->indentLevel--;
            $this->write( $this->indentCode( '}' . $this->lineBreak ) );
        }
        else
        {
            $this->abort();
            $current = $pop ? $pop : 'no control structure';
            throw new ezcPhpGeneratorFlowException( $current, $type );
        }
    }
    /**
     * Writes $data to $this->fileResource
     *
     * @throws ezcPhpGeneratorException if it was not possible to write to the file.
     * @param string $data
     * @return void
     */
    protected function write( $data )
    {
        if ( !$this->fileResource )
        {
            throw new ezcBaseFileIoException( $this->tmpFilename, ezcBaseFileException::WRITE,
                                              'ezcPhpGenerator could not write to the temporary file. It has already been closed.' );
        }

        if ( fwrite( $this->fileResource, $data ) === false )
        {
            throw new ezcBaseFileIoException( $this->tmpFilename, ezcBaseFileException::WRITE,
                                              'ezcPhpGenerator could not write to the temporary file.' );
        }
    }

    /**
     * Returns each line in $text indented correctly if indenting is turned on.
     *
     * If indenting is turned off it will return $text unmodified.
     *
     * @param string $text
     * @return string
     */
    protected function indentCode( $text )
    {
        if ( $this->niceIndentation == false || $this->indentLevel == 0 )
            return $text;

        $textArray = explode( $this->lineBreak, $text );
        $newTextArray = array();
        foreach ( $textArray as $text )
        {
            if ( trim( $text ) != ''  )
            {
                $textLine = str_repeat( $this->indentString, $this->indentLevel ) . $text;
            }
            else
            {
                $textLine = $text;
            }
            $newTextArray[] = $textLine;
        }
        return implode( $this->lineBreak, $newTextArray );
    }
}
?>
