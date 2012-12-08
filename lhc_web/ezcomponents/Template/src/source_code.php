<?php
/**
 * File containing the ezcTemplateSourceCode class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Encapsulates information on a template file containing source code.
 *
 * This class is used by the manager when it parses template code and can be
 * used by code to read and write template code or remove template files in
 * a convenient way.
 *
 * Instantiate this class with the stream string and the optional arguments,
 * then call either load(), save() or delete() to perform the requested action.
 * The class will throw exceptions for failures so it is a good idea to call
 * isReadable(), isWriteable() or isAvailable() before any of these actions.
 *
 * For instance to display the content of a template file one can do:
 * <code>
 * $source = new ezcTemplateSourceCode( "templates/main.tpl" );
 * if ( $source->isReadable() )
 * {
 *    $source->load();
 *    echo $source->code;
 * }
 * else
 * {
 *    echo "Cannot load template ", $source->stream, "\n";
 * }
 * </code>
 *
 * Similarly one can create template files with:
 * <code>
 * $source = new ezcTemplateSourceCode( "templates/left.tpl" );
 * $source->code = '{$left|str_capitalize}';
 * if ( !$source->isAvailable() )
 * {
 *    $source->save();
 * }
 * else if ( $source->isWriteable() )
 * {
 *    $source->save();
 * }
 * else
 * {
 *    echo "Cannot save template ", $source->stream, "\n";
 * }
 * </code>
 *
 * To check if any source code has been loaded or set use hasCode().
 *
 * @property string $stream  
 *              The PHP stream path for the template source file.
 * @property string $resource 
 *              The resource string which requested this template.
 * @property string $code
 *              The original template code taken from the template file or
 *              other resource. Contains a string with the source code or
 *              false if no code is read yet.
 * @property ezcTemplateOutputContext $context 
 *              The current context for the template code. Will be used for
 *              parsing and run-time behaviour.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateSourceCode
{
    /**
     * Array that stores the property values.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

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
            case 'stream':
            case 'resource':
            case 'code':
            case 'context':
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
            case 'stream':
            case 'resource':
            case 'code':
            case 'context':
                $this->properties[$name] = $value;
                break;
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
            case 'stream':
            case 'resource':
            case 'code':
            case 'context':
                return true;
            default:
                return false;
        }
    }

    /**
     * Initialises the source object with the code and output context.
     *
     * @param string $stream The actual PHP stream path for the template source
     *                       file.
     * @param string $resource The requested resource string, if false $stream
     *                         is used as value.
     * @param string $code The source code for the template.
     * @param ezcTemplateOutputContext $context The context for the parsing and
     *                                    run-time behaviour, a value of null
     *                                    means to use the current context in
     *                                    the template manager.
     */
    public function __construct( $stream, $resource = false, $code = false, ezcTemplateOutputContext $context = null )
    {
        $this->stream = $stream;
        $this->resource = $resource;
        $this->code = $code;
        $this->context = $context;
    }

    /**
     * Loads the data from the PHP stream into the $code member variable.
     *
     * @throws ezcTemplateFileNotFoundException if the file does not exist on disk.
     * @throws ezcTemplateFileNotReadableException if the file cannot be read.
     *
     * @see isAvailable(), isReadable()
     *
     * Note: Calling this multiple times will re-init the $source variable.
     *
     * @return void
     */
    public function load()
    {
        if ( !file_exists( $this->stream ) )
            throw new ezcTemplateFileNotFoundException( $this->stream );
        if ( !is_readable( $this->stream ) )
            throw new ezcTemplateFileNotReadableException( $this->stream );
        $this->code = file_get_contents( $this->stream );
    }

    /**
     * Saves the data from $code member variable to the PHP stream.
     *
     * This method creates a backup from the exisiting template. The name of the
     * backup template appends a tilde (~). If a backup already exists,
     * this method overwrites the old backup.
     *
     * @throws ezcTemplateFileNotWritableException if the file cannot be written to.
     *
     * @see isWriteable()
     * Note: Storing the data will not record the template file in the system or
     * signal the changes, call the template manager to perform these tasks.
     *
     * Note: Calling this multiple times will overwrite the file contents over and
     * over again. And the backup contains the same information as the original.
     *
     * @return void
     */
    public function save()
    {
        if ( file_exists( $this->stream ) && !is_writeable( $this->stream ) )
            throw new ezcTemplateFileNotWriteableException( $this->stream );

        // Store data in a temporary file
        $tempName = dirname( $this->stream ) . '/#' .  basename( $this->stream ) . '#';
        if ( file_put_contents( $tempName, $this->code, LOCK_EX ) === false )
            throw new ezcTemplateFileNotWriteableException( $this->stream );

        $backupName = $this->stream . '~';
        // Remove old backup (if it exists)
        if ( file_exists( $backupName ) &&
             !unlink( $backupName ) )
        {
            unlink( $tempName );
            throw new ezcTemplateFileFailedUnlinkException( $backupName );
        }

        // Make the current file (if it exists) a backup
        if ( file_exists( $this->stream ) &&
             !rename( $this->stream, $backupName ) )
        {
            unlink( $tempName );
            throw new ezcTemplateFileRenameFailedException( $this->stream, $backupName );
        }

        // Make the temporary file the current one
        if ( !rename( $tempName, $this->stream ) )
        {
            unlink( $tempName );
            rename( $backupName, $this->stream );
            throw new ezcTemplateFileRenamedFailedException( $tempName, $this->stream );
        }
    }

    /**
     * Deletes the file from the file system.
     *
     * @throws ezcTemplateFileNotFoundException if the file does not exist on disk.
     * @throws ezcTemplateFileFailedUnlinkException if the file could not be unlinked.
     *
     *
     * @see isAvailable()
     *
     * @return void
     */
    public function delete()
    {
        if ( !file_exists( $this->stream ) )
            throw new ezcTemplateFileNotFoundException( $this->stream );
        if ( !unlink( $this->stream ) )
            throw new ezcTemplateFileFailedUnlinkException( $this->stream );
    }

    /**
     * Checks if the template file exists on disk and can be read.
     *
     * @return bool
     */
    public function isAvailable()
    {
        return file_exists( $this->stream );
    }

    /**
     * Checks if the template file can be read from.
     *
     * @return bool
     */
    public function isReadable()
    {
        return is_readable( $this->stream );
    }

    /**
     * Checks if the template file can be written to.
     *
     * @return bool
     */
    public function isWriteable()
    {
        return is_writeable( $this->stream );
    }

    /**
     * Checks if source code has been loaded from the template file or set by PHP
     * code.
     *
     * @return bool
     */
    public function hasCode()
    {
        return $this->code !== false;
    }

}
?>
