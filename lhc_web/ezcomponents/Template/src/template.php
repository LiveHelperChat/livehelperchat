<?php
/**
 * File containing the ezcTemplate class.
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The main class for processing templates.
 *
 * The ezcTemplate class compiles a source template (*.ezt) to PHP code,
 * executes the PHP code, and returns the output. The generated PHP code 
 * will be stored on disk as a compiled template.
 * 
 * If a compiled template already exists of the to process template, the 
 * ezcTemplate class executes directly the compiled template; thus omitting
 * the compile step.
 *
 * A simple invocation is simply to create the template object and call the
 * process() method, e.g.
 * <code>
 * $t = new ezcTemplate();
 * echo $t->process( "page.ezt" );
 * </code>
 *
 * The location for the source templates and compiled templates among other things 
 * are specified in the ezcTemplateConfiguration configuration object. A default
 * configuration is always present and can be accessed via the $configuration
 * property.
 *
 * Usually one configuration object will be enough, since most of the templates
 * will use the same configuration settings. If for some reason, other configuration
 * settings are needed you can assign an ezcTemplateConfiguration object to the $configuration property.
 *
 * The following example shows how to change the template and compilation
 * directory by creating a new configuration object.
 * <code>
 * $t = new ezcTemplate();
 * $t->configuration = new ezcTemplateConfiguration( "design/templates",
 *                                                   "/tmp/compilation" );
 * echo $t->process( "page.ezt" );
 * </code>
 *
 * Another approach is to pass the ezcTemplateConfiguration object to the process method. This
 * method will use the given configuration instead.
 * <code>
 * $t = new ezcTemplate();
 * $config = new ezcTemplateConfiguration( "design/templates",
 *                                         "/tmp/compilation" );
 * echo $t->process( "page.ezt", $config );
 * </code>
 *
 * The properties {@link ezcTemplate::send send} and
 * {@link ezcTemplate::receive receive} are available to set the variables that
 * are sent to and retrieved from the template.
 *
 * The next example demonstrates how a template variable is set and retrieved:
 * 
 * <code>
 * $t = new ezcTemplate();
 *
 * $t->send->mySentence = "Hello world";
 * echo $t->process( "calc_sentence_length.ezt" );
 *
 * $number = $t->receive->length;
 * </code>
 * 
 * The template code:
 * <code>
 * {use $mySentence = ""}
 * 
 * {var $length = str_len( $mySentence )}
 * {return $length}
 * </code>
 * 
 * @property ezcTemplateVariableCollection $send
 *                Contains the variables that are send to the template.
 * @property-read ezcTemplateVariableCollection $receive
 *                Contains the variables that are returned by the template.
 * @property ezcTemplateConfiguration      $configuration
 *                Contains the template configuration.
 * @property-read string                        $output
 *                The output of the last processed template.
 * @property-read string                        $compiledTemplatePath
 *                The path of the compiled template.
 * @property-read ezcTemplateTstNode            $tstTree
 *                The generated tstTree (debug).
 * @property-read ezcTemplateAstNode            $astTree
 *                The generated astTree (debug).
 * @package Template
 * @version 1.4.2
 * @mainclass
 */
class ezcTemplate
{
    /**
     * An array containing the properties of this object:
     *
     * The stream and streamStack properties are internal only, and should not
     * be used externally.
     *
     * @var array(string=>mixed)
     */
    private $properties = array( 'configuration' => null,
                                 'usedConfiguration' => null,
                                 'send' => null,
                                 'receive' => null, 
                                 'compiledTemplatePath' => null,
                                 'tstTree' => false,
                                 'astTree' =>  false,
                                 'stream'  => false,
                                 'streamStack' => false,
                                 'output' => "",
                                 'trimWhitespace' => true,
                               );

    /**
     * Returns the value of the property $name.
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
            case 'send': 
            case 'receive':
            case 'tstTree':
            case 'astTree':
            case 'stream':
            case 'streamStack':
            case 'compiledTemplatePath':
            case 'usedConfiguration':

            case 'output':
            case 'trimWhitespace':
                return $this->properties[$name];

            case 'configuration':
                if ( $this->properties[$name] === null )
                {
                       $this->properties[$name] = ezcTemplateConfiguration::getInstance();
                        if ( get_class( $this->properties[$name] ) != 'ezcTemplateConfiguration' )
                        {
                            throw new ezcTemplateInternalException( "Static method ezcTemplateConfiguration::getInstance() did not return an object of class ezcTemplateConfiguration" );
                        }
                }
                return $this->properties[$name];


            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Checks if the property $name is set and returns the result.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'configuration':
            case 'usedConfiguration':
            case 'compiledTemplatePath':
            case 'send':
            case 'receive':
            case 'tstTree':
            case 'astTree':
            case 'stream':
            case 'streamStack':
            case 'output':
            case 'trimWhitespace':
                return isset( $this->properties[$name] );
            default:
                return false;
        }
    }

    /**
     * Sets the property named $name to contain value of $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @return void
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'send': 
                if ( !$value instanceof ezcTemplateVariableCollection )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcTemplateVariableCollection' );
                } 
                $this->properties[$name] = $value; 
                break;

            case 'configuration':
            case 'usedConfiguration':
                if ( $value !== null and !( $value instanceof ezcTemplateConfiguration ) )
                {
                     throw new ezcBaseValueException( $name, $value, 'ezcTemplateConfiguration' );
                }
                $this->properties[$name] = $value;
                break;

            case 'tstTree':
            case 'astTree':
            case 'compiledTemplatePath':
            case 'output':
            case 'stream':
            case 'streamStack':
            case 'receive':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );


            default:
                // case 'usedConfiguration':
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Intializes the ezcTemplate with a default configuration and empty
     * $send and $receive properties.
     *
     * Configuration of the object must now be done through the properties:
     * - {@link ezcTemplate::configuration configuration}
     * - {@link ezcTemplate::send send}
     * - {@link ezcTemplate::receive receive}
     */
    public function __construct()
    {
        $this->properties["send"] = new ezcTemplateVariableCollection();
        $this->properties["receive"] = new ezcTemplateVariableCollection();
    }

    /**
     * Processes the specified template source and returns the output string.
     *
     * Note: The first time a template is accessed it needs to be compiled so the
     * execution time will be higher than subsequent calls.
     *
     * @param string $location The path to the template file to process, can be a PHP stream.
     * @param ezcTemplateConfiguration $config Optional configuration object which overrides
     *                                         the default one defined in this object ($configuration).
     * @return string
     *
     * @apichange Remove the test for ezcTemplateLocationInterface as it's deprecated.
     *
     * @throws ezcTemplateParserException
     *         If the template could not be compiled.
     * @throws ezcTemplateFileNotWriteableException
     *         If the directory could not be created.
     */
    public function process( $location, ezcTemplateConfiguration $config = null )
    {
        if ( $config === null )
        {
            $config = $this->configuration;
        }

        $this->properties["usedConfiguration"] = $config;
        $this->properties["tstTree"] = false;
        $this->properties["astTree"] = false;
        $this->properties["stream"] = $location;
        $this->properties['trimWhitespace'] = $config->trimWhitespace;

        if ( $location instanceof ezcTemplateLocation || $location instanceof ezcTemplateLocationInterface )
        {
            $this->properties["file"] = $location;
            $this->properties["stream"] = $location->getPath();
        } 
        elseif ( $config->locator )
        {
            $this->properties["stream"] = $config->locator->translatePath($this->properties["stream"]);
        }
        
        if ( strlen( $this->properties["stream"] ) > 0 && !ezcBaseFile::isAbsolutepath($this->properties["stream"]) ) // Is it a relative path?
        {
            $this->properties["stream"] = $config->templatePath . DIRECTORY_SEPARATOR . $this->properties["stream"];
        }
        $this->properties["streamStack"][] = $this->properties["stream"];

        // lookup compiled code here
        $compiled = ezcTemplateCompiledCode::findCompiled( $this->properties["stream"], $config->context, $this );
        $this->properties["compiledTemplatePath"] = $compiled->path;

        $counter = 0;
        while ( true )
        {
            ++$counter;
            if ( $counter > 3 )
            {
                // @todo fix exception
                throw new ezcTemplateCompilationFailedException( "Failed to create and execute compiled code after " . ($counter - 1) . " tries." );
            }

            if ( file_exists( $compiled->path ) && 
               ( !$config->checkModifiedTemplates || 
                  filemtime( $this->properties["stream"] ) <= filemtime( $compiled->path ) ) ) 
            {
                if ( !$config->executeTemplate )
                {
                    $this->properties["output"] = "";
                    return "";
                }

                try
                {
                    // execute compiled code here
                    $this->properties["output"] = $compiled->execute();
                    return $this->properties["output"];
                }
                catch ( ezcTemplateOutdatedCompilationException $e )
                {
                    // The compiled file cannot be used so we need to recompile it
                }
            }

            $this->createDirectory( dirname( $compiled->path ) );

            // get the compiled path.
            // use parser here
            $source = new ezcTemplateSourceCode( $this->properties["stream"], $this->properties["stream"] );
            $source->load();
            $parser = new ezcTemplateParser( $source, $this );
            $this->properties["tstTree"] = $parser->parseIntoNodeTree();

            if ($parser->hasCacheBlocks && !$config->disableCache )
            {
                $fetchCacheInfo = new ezcTemplateFetchCacheInformation(); 
                $this->properties["tstTree"]->accept( $fetchCacheInfo );

                $tstToAst = new ezcTemplateTstToAstCachedTransformer( $parser, $fetchCacheInfo->cacheTst );
            }
            else
            {
                $tstToAst = new ezcTemplateTstToAstTransformer( $parser );
            }

            $this->properties["tstTree"]->accept( $tstToAst );
            $this->properties["astTree"] = $tstToAst->programNode;

            $astToAst = new ezcTemplateAstToAstContextAppender( $config->context );
            $tstToAst->programNode->accept( $astToAst );

            // Extra optimization.
            $astToAst = new ezcTemplateAstToAstAssignmentOptimizer();
            $tstToAst->programNode->accept( $astToAst );

            $g = new ezcTemplateAstToPhpGenerator( $compiled->path, $config ); // Write to the file.
            $tstToAst->programNode->accept( $g );

            // Add to the cache system.
            if ( $config->cacheManager )
            {
                $config->cacheManager->includeTemplate( $this, $this->properties["stream"] );
            }
        }

        // execute compiled code here
        throw new ezcTemplateInternalException( "Compilation or execution failed" );
    }


    /**
     * Creates the directory $path if it does not exist.
     *
     * If the directory $path could be created the function returns true,
     * otherwise the ezcTemplateFileNotWriteableException exception is thrown.
     *
     * @throws ezcTemplateFileNotWriteableException
     *         If the directory could not be created
     * @param string $path
     * @return bool
     */
    private function createDirectory( $path )
    {
        if ( !is_dir( $path ) )
        {
            $created = @mkdir( $path, 0777, true );
            if ( !$created )
            {
                throw new ezcTemplateFileNotWriteableException( $path );
            }
        }

        return true;
    }

    /**
     * Generates a unique hash from the current options.
     *
     * For example the default values would return:
     * <code>
     * "updqr0"
     * </code>
     *
     * Note: This is mostly useful for the template component,
     *       relying on the output of this function is not a good idea.
     *
     * @return string
     */
    public function generateOptionHash()
    {
        // We have to shift in two steps as << 32 does not work on PHP on 32 bit machines
        // We are shifting to get correct negative number.
        return base_convert( ( -1 << 1 << 31 ) | crc32( 'ezcTemplate::options(' .
                                    false /*(bool)$this->outputDebugEnabled*/ . '-' .
                                    false /*(bool)$this->compiledDebugEnabled*/ . ')' ),
                             10, 36 );
    }
}
?>
