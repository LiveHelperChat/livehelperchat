<?php
/**
 * File containing the ezcTemplateCompiledCode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Represents a compiled PHP file on the filesystem which can be generated or
 * executed.
 *
 * If you want to know if a compiled file exists and can be used create a new
 * instance of this and check the isValid() flag.
 *
 * If you are unsure where the compiled file resides you can use the static
 * methods findCurrent() and findAll() to get those identifiers.
 *
 * @property-read string $identifier 
 *              The unique identifier for the compiled file.
 * @property-read string $path       
 *              The complete (but relative) path to the compiled file. Will
 *              be set even if it does not exist.
 * @property ezcTemplateOutputContext $context  
 *              The context used for the currently compiled file.
 * @property ezcTemplate $template  
 *              The template which is used when executing the template code.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCompiledCode
{
    /**
     * The unique number for the template engine, this will be increased each time
     * the compiled code needs to be recompiled at the client.
     */
    const ENGINE_ID = 1;

    /**
     * An array containing the properties of this object.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * @var ezcTemplateVariableCollection
     */
    private $send;

    /**
     * @var ezcTemplateVariableCollection
     */
    private $receive;


    /**
     * Property get
     *
     * @param string $name
     * @return mixed
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'identifier':
            case 'path':
            case 'context':
            case 'template':
                return $this->properties[$name];
            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Property set
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'context':
                if ( $value !== null and
                     !( $value instanceof ezcTemplateOutputContext ) )
                     throw new ezcBaseValueException( $name, $value, 'ezcTemplateOutputContext' );
                $this->properties[$name] = $value;
                break;
            case 'template':
                if ( $value !== null and
                     !( $value instanceof ezcTemplate ) )
                     throw new ezcBaseValueException( $name, $value, 'ezcTemplate' );
                $this->properties[$name] = $value;
                break;
            case 'identifier':
            case 'path':
                throw new ezcBasePropertyPermissionException( $name, ezcBasePropertyPermissionException::READ );
            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Property isset
     *
     * @param string $name
     * @return bool
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'identifier':
            case 'path':
            case 'context':
            case 'template':
                return true;
            default:
                return false;
        }
    }

    /**
     * Initialises the object with the identifier and the full path to the PHP file.
     *
     * @param string $identifier
     * @param string $path
     * @param ezcTemplateOutputContext $context
     * @param ezcTemplate $template
     */
    public function __construct( $identifier, $path,
                                 /*ezcTemplateOutputContext*/ $context = null, ezcTemplate $template = null )
    {
        $this->properties['identifier'] = $identifier;
        $this->properties['path'] = $path;
        $this->context = $context;
        $this->template = $template;
    }

    /**
     * Executes the current compiled file using the source object.
     *
     * The input template variables is taken from the template.
     *
     * @throws ezcTemplateNoManagerException if there is no template set.
     * @throws ezcTemplateNoOutputContextException if there is no output context set.
     * @throws ezcTemplateInvalidCompiledFileException if the compiled cannot be executed.
     *
     * @return string
     */
    public function execute()
    {
        if ( $this->template === null )
            throw new ezcTemplateNoManagerException( __CLASS__, 'template' );
        if ( $this->context === null )
            throw new ezcTemplateNoOutputContextException( __CLASS__, 'context' );

        if ( !$this->isValid() )
            throw new ezcTemplateInvalidCompiledFileException( $this->identifier, $this->path );
        
            $this->send = clone $this->template->send;
            $this->receive = $this->template->receive;
        return include( $this->path );
    }


    /**
     * The compiled template calls this method to see if the current template should be recompiled.
     * Usually its the first method called in the template. 
     *
     * A template should be recompiled if either one of the following rules apply:
     * - The template source is newer than the compiled template.
     * - The ENGINE_ID in the template (and given as parameter) is different than the ezcTemplateCompiledCode::ENGINE_ID.
     *
     * @param int $engineID
     *
     * @throws ezcTemplateOutdatedCompilationException when the template should be recompiled. 
     * @return void
     */
    protected function checkRequirements( $engineID, $compileFlags = array() )
    {
        if ( $this->template->configuration->checkModifiedTemplates &&
             // Do not recompile when the modification times are the same. This messes up the caching tests.
             file_exists( $this->template->stream ) &&
             filemtime( $this->template->stream ) > filemtime( $this->properties['path'] ) )
        {
            throw new ezcTemplateOutdatedCompilationException( "The source template file '{$this->template->stream}' is newer than '{$this->path}', will recompile." );
        }

        // Check if caching is enabled
        if ( isset( $compileFlags["disableCache"] ) )
        {
            if ( $this->template->configuration->disableCache != $compileFlags["disableCache"] )
            {
                throw new ezcTemplateOutdatedCompilationException( "The compileFlag 'disableCache' has been changed, will recompile." );
            }
        }

        // Check if the engine ID differs
        if ( $engineID !== self::ENGINE_ID )
        {
            throw new ezcTemplateOutdatedCompilationException( "The compilation file '{$this->path}' is outdated, will recompile." );
        }
    }

    /**
     * Returns true if the compiled code exists and is valid for execution.
     *
     * @return bool
     */
    public function isValid()
    {
        return file_exists( $this->path ) and is_readable( $this->path );
    }

    /**
     * Returns true if the compiled code exists on the filesystem.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return file_exists( $this->path );
    }

    /**
     * Finds the compiled file based on the stream path and template options.
     *
     * Returns the compiled code object which can be used for execution or queried for more info.
     *
     * @param string $location The stream path of the requested template file.
     * @param ezcTemplateOutputContext $context The current output context handler.
     * @param ezcTemplateManager $template The template which contains the current settings.
     * @return ezcTemplateCompiledCode
     */
    public static function findCompiled( $location, ezcTemplateOutputContext $context, ezcTemplate $template )
    {
        $options = 'ezcTemplate::options(' .
                   false /*(bool)$template->outputDebugEnabled*/ . '-' .
                   false /*(bool)$template->compiledDebugEnabled*/ . ')';
        $identifier = md5( 'ezcTemplateCompiled(' . $location . ')' );
        $name = basename( $location, '.ezt' );

        $path = $template->configuration->compilePath . DIRECTORY_SEPARATOR . $template->configuration->compiledTemplatesPath . DIRECTORY_SEPARATOR .
                $context->identifier() . '-' .
                $template->generateOptionHash() . '/' .
                $name . '-' . $identifier . ".php";
        return new ezcTemplateCompiledCode( $identifier, $path, $context, $template );
    }
}
?>
