<?php
/**
 * File containing the ezcPersistentObjectSchemaGenerator class
 *
 * @package PersistentObjectDatabaseSchemaTiein
 * @version 1.3
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcPersistentObjectSchemaGenerator is capable to generate PersistentObject
 * definition files from DatabaseSchema definitions.
 *
 * This is the main class of the PersistentObjectDatabaseSchemaTiein package. It
 * implemets the generator class itself. To run the generator, please use the 
 * file "rungenerator.php", inside this package.
 *
 * To generate PersistentObject definitions from a DatabaseSchema, use the
 * following synopsis:
 *
 * <code>
 * $ php PersistentObjectDatabaseSchemaTiein/src/rungenerator.php -s path/to/schema.file -f xml path/to/persistentobject/defs/
 * </code>
 *
 * The -s / --source parameter points to the source schema file. The -f / --format
 * option specifies the format the schema file has. The argument for the program 
 * specifies the directory, where the PersistentObject definitions will be stored.
 *
 * For help information simply call
 * <code>
 * $ php PersistentObjectDatabaseSchemaTiein/src/rungenerator.php
 * </code>
 * or
 * <code>
 * $ php PersistentObjectDatabaseSchemaTiein/src/rungenerator.php -h
 * </code>
 * for extended help information.
 *
 * @package PersistentObjectDatabaseSchemaTiein
 * @version 1.3
 */
class ezcPersistentObjectSchemaGenerator
{

    /**
     * The console input handler.
     * 
     * @var ezcConsoleInput
     */
    private $input;

    /**
     * The console output handler.
     * 
     * @var ezcConsoleOutput
     */
    private $output;


    const PROGRAM_DESCRIPTION = 'Generates defition files for the eZ PersistentObject package from eZ DatabaseSchema formats. The directory to save the definition files to is provided as an argument.';

    /**
     * Create a new generator.
     * This method initializes the necessary objects to run the application.
     * 
     * @return void
     */
    public function __construct()
    {
        $schemaFormats = implode( ", ", ezcDbSchemaHandlerManager::getSupportedFormats() );

        $this->output = new ezcConsoleOutput();

        $this->output->options->autobreak = 80;
        
        $this->output->formats->info->color = 'blue';
        $this->output->formats->info->style = array( 'bold' );

        $this->output->formats->help->color = 'blue';

        $this->output->formats->error->color = 'red';
        $this->output->formats->error->style = array( 'bold' );

        $this->output->formats->success->color = 'green';
        $this->output->formats->success->style = array( 'bold' );
        
        $this->input = new ezcConsoleInput();

        $this->input->registerOption(
            new ezcConsoleOption(
                "s",        // short
                "source",   // long
                ezcConsoleInput::TYPE_STRING,
                null,       // default
                false,      // multiple
                "DatabaseSchema source to use.",
                "The DatabaseSchema to use for the generation of the PersistentObject definition. Or the DSN to the database to grab the schema from.",
                array(),    // dependencies
                array(),    // exclusions
                true,       // arguments
                true        // mandatory
            )
        );

        $this->input->registerOption(
            new ezcConsoleOption(
                "f",        // short
                "format",   // long
                ezcConsoleInput::TYPE_STRING,
                null,       // default
                false,      // multiple
                "DatabaseSchema format of the input source.",
                "The format, the input DatabaseSchema is in. Valid formats are {$schemaFormats}.",
                array(),    // dependencies
                array(),    // exclusions
                true,       // arguments
                true        // mandatory
            )
        );
        
        $this->input->registerOption(
            new ezcConsoleOption(
                "o",        // short
                "overwrite",   // long
                ezcConsoleInput::TYPE_NONE,
                null,       // default
                false,      // multiple
                "Overwrite existing files.",
                "If this option is set, files will be overwriten if they alreday exist."
            )
        );

        $this->input->registerOption(
            new ezcConsoleOption(
                "p",        // short
                "prefix",   // long
                ezcConsoleInput::TYPE_STRING,
                null,       // default
                false,      // multiple
                "Class prefix.",
                "Unique prefix that will be prepended to all class names.",
                array(),    // dependencies
                array(),    // exclusions
                true,       // arguments
                false       // mandatory
            )
        );

        $this->input->registerOption(
            new ezcConsoleOption(
                "t",        // short
                "template",   // long
                ezcConsoleInput::TYPE_NONE,
                null,       // default
                false,      // multiple
                "Use template rendering.",
                "Switch on template rendering. Use --class-template, --definition-template, --template-path to customize.",
                array(),    // dependencies
                array(),    // exclusions
                true,       // arguments
                false       // mandatory
            )
        );


        $this->input->registerOption(
            new ezcConsoleOption(
                "ct",       // short
                "class-template",   // long
                ezcConsoleInput::TYPE_STRING,
                'class_template.ezt',   // default
                false,      // multiple
                "Class template.",
                "Template file to use for writing class stubs, defaults to eZ Components style classes. Look at default template to customize.",
                array(),    // dependencies
                array(),    // exclusions
                true,       // arguments
                false       // mandatory
            )
        );

        $this->input->registerOption(
            new ezcConsoleOption(
                "dt",       // short
                "definition-template",   // long
                ezcConsoleInput::TYPE_STRING,
                'definition_template.ezt',       // default
                false,      // multiple
                "Definition template.",
                "Template file to use for writing definition stubs, defaults configs fitting the default class template. Look at default template to customize.",
                array(),    // dependencies
                array(),    // exclusions
                true,       // arguments
                false       // mandatory
            )
        );

        $this->input->registerOption(
            new ezcConsoleOption(
                "tp",       // short
                "template-path",   // long
                ezcConsoleInput::TYPE_STRING,
                dirname( __FILE__ ) . '/template_writer/templates',       // default
                false,      // multiple
                "Base template path.",
                "Path where templates are located. Will also by used as the path to compile templates to.",
                array(),    // dependencies
                array(),    // exclusions
                true,       // arguments
                false       // mandatory
            )
        );

        $this->input->registerOption(
            new ezcConsoleOption(
                "h",        // short
                "help",     // long
                ezcConsoleInput::TYPE_NONE,
                null,       // default
                false,      // multiple
                "Retrieve detailed help about this application.",
                "Print out this help information.",
                array(),    // dependencies
                array(),    // exclusions
                true,       // arguments
                false,      // mandatory
                true        // help option
            )
        );

        $this->input->getOption( 'template-path' )->addDependency(
            new ezcConsoleOptionRule( $this->input->getOption( 'template' ) )
        );

        $this->input->getOption( 'definition-template' )->addDependency(
            new ezcConsoleOptionRule( $this->input->getOption( 'template' ) )
        );

        $this->input->getOption( 'class-template' )->addDependency(
            new ezcConsoleOptionRule( $this->input->getOption( 'template' ) )
        );

        $this->input->argumentDefinition = new ezcConsoleArguments();

        $this->input->argumentDefinition[0] = new ezcConsoleArgument( "def dir" );
        $this->input->argumentDefinition[0]->shorthelp = "PersistentObject definition directory.";
        $this->input->argumentDefinition[0]->longhelp  = "Directory where PersistentObject definitions will be stored.";

        $this->input->argumentDefinition[1] = new ezcConsoleArgument( "class dir" );
        $this->input->argumentDefinition[1]->mandatory = false;
        $this->input->argumentDefinition[1]->shorthelp = "Class directory.";
        $this->input->argumentDefinition[1]->longhelp  = "Directory where PHP classes will be stored. Classes will not be generated if this argument is ommited.";
        
        $this->output->outputLine( 'eZ components PersistentObject definition generator', 'info' );
        $this->output->outputLine();
    }

    /**
     * Run the generator.
     * Process the given options and generate a PersistentObject definition from it.
     * 
     * @return void
     */
    public function run()
    {
        try
        {
            $this->input->process();
        }
        catch ( ezcConsoleException $e )
        {
            $this->raiseError( "Error while processing your options: {$e->getMessage()}", true );
        }

        if ( $this->input->getOption( 'h' )->value === true )
        {
            $this->output->outputText( 
                $this->input->getHelpText( 
                    ezcPersistentObjectSchemaGenerator::PROGRAM_DESCRIPTION,
                    80,
                    true
                ), 
                "help" 
            );
            exit( 0 );
        }

        $defDir   = $this->input->argumentDefinition["def dir"]->value;
        $classDir = $this->input->argumentDefinition["class dir"]->value;

        try
        {
            $schema = $this->getSchema(
                $this->input->getOption( "format" )->value,
                $this->input->getOption( "source" )->value
            );
        }
        catch ( Exception $e )
        {
            $this->raiseError( "Error reading schema: {$e->getMessage()}" );
        }

        try
        {
            $this->writeConfigFiles( $defDir, $schema );
            if ( $classDir !== null)
            {
                $this->writeClassFiles( $classDir, $schema );
            }
        }
        catch ( Exception $e )
        {
            $this->raiseError( "Error writing schema: {$e->getMessage()}" );
        }

        $this->output->outputLine(
            "PersistentObject definition successfully written to {$defDir}.",
            'info'
        );
        if ( $classDir !== null )
        {
            $this->output->outputLine(
                "Class files successfully written to {$classDir}.",
                'info'
            );
        }
    }

    /**
     * Write config files from $schema to $defDir.
     *
     * Writes the PersistentObject definition files for $schema to $defDir.
     * 
     * @param string $defDir 
     * @param ezcDbSchema $schema 
     * @return void
     */
    private function writeConfigFiles( $defDir, ezcDbSchema $schema )
    {
        if ( $this->input->getOption( 'template' )->value !== false )
        {
            $this->writeFromTemplate(
                $defDir,
                $schema,
                $this->input->getOption( 'definition-template' )->value
            );
        }
        else
        {
            $this->writeConfigTraditional( $defDir, $schema );
        }
    }

    /**
     * Writes the config for $schema to $defDir in the traditional way.
     *
     * Writes the PersistentObject definition files for $schema to $defDir
     * using {@link ezcDbSchemaPersistentWriter} from the DatabaseSchema
     * component.
     * 
     * @param string $defDir 
     * @param ezcDbSchema $schema 
     */
    private function writeConfigTraditional( $defDir, ezcDbSchema $schema )
    {
        $writer = new ezcDbSchemaPersistentWriter(
            $this->input->getOption( "overwrite" )->value,
                $this->input->getOption( "prefix" )->value
        );
        $writer->saveToFile( $defDir, $schema );
    }

    /**
     * Write class files for $schema to $classDir.
     *
     * Creates PersistentObject class stubs from $schema in $classDir.
     * 
     * @param string $classDir 
     * @param ezcDbSchema $schema 
     */
    private function writeClassFiles( $classDir, $schema )
    {
        if ( $this->input->getOption( 'template' )->value !== false )
        {
            $this->writeFromTemplate(
                $classDir,
                $schema,
                $this->input->getOption( 'class-template' )->value
            );
        }
        else
        {
            $this->writeClassesTraditional( $classDir, $schema );
        }
    }

    /**
     * Writes the classes for $schema to $defDir in the traditional way.
     *
     * Writes the PersistentObject class stubs for $schema to $classDir using
     * {@link ezcDbSchemaPersistentClassWriter} from the DatabaseSchema
     * component.
     * 
     * @param string $classDir
     * @param ezcDbSchema $schema 
     */
    private function writeClassesTraditional(  $classDir, $schema )
    {
        $writer = new ezcDbSchemaPersistentClassWriter(
            $this->input->getOption( "overwrite" )->value,
            $this->input->getOption( "prefix" )->value
        );
        $writer->saveToFile( $classDir, $schema );
    }

    /**
     * Writes classes or configuration using a template.
     *
     * This method uses the given $tpl file to output $schema to $dir. $tpl is
     * either a template for PersistentObject definition files or
     * PersistentObject class stubs.
     * 
     * @param string $dir 
     * @param ezcDbSchema $schema 
     * @param string $tpl 
     */
    private function writeFromTemplate( $dir, $schema, $tpl )
    {
        $writer = new ezcPersistentObjectTemplateSchemaWriter();

        if ( ( $tplPath = $this->input->getOption( 'template-path' )->value ) !== false )
        {
            $writer->options->templatePath = $tplPath;
        }
        if ( ( $prefix = $this->input->getOption( 'prefix' )->value ) !== false )
        {
            $writer->options->classPrefix = $prefix;
        }
        if ( ( $overwrite = $this->input->getOption( 'overwrite' )->value ) !== false )
        {
            $writer->options->overwrite = $overwrite;
        }

        $writer->write( $schema, $tpl, $dir );
    }

    /**
     * Returns an {@link ezcDbSchema} created from $source which is of $format.
     * 
     * @param string $format 
     * @param string $source 
     * @return ezcDbSchema
     */
    private function getSchema( $format, $source )
    {
        $readerClass = ezcDbSchemaHandlerManager::getReaderByFormat(
            $format
        );
        $reader = new $readerClass();

        $schema = null;

        switch ( true )
        {
            case ( $reader instanceof ezcDbSchemaDbReader ):
                $db     = ezcDbFactory::create( $source );
                $schema = ezcDbSchema::createFromDb( $db );
                break;
            case ( $reader instanceof ezcDbSchemaFileReader ):
            default:
                $schema = ezcDbSchema::createFromFile( $format, $source );
                break;
        }

        return $schema;
    }

    /**
     * Prints the message of an occured error and exits the program.
     * This method is used to print an error message, as soon as an error
     * occurs end quit the program with return code -1. Optionally, the
     * method will trigger the help text to be printed after the error message.
     * 
     * @param string $message The error message to print
     * @param bool $printHelp Whether to print the help after the error msg.
     */
    private function raiseError( $message, $printHelp = false )
    {
        $this->output->outputLine( $message, 'error' );
        $this->output->outputLine();
        if ( $printHelp === true )
        {
            $this->output->outputText( 
                $this->input->getHelpText( 
                    ezcPersistentObjectSchemaGenerator::PROGRAM_DESCRIPTION
                ), 
                "help" 
            );
        }
        exit( -1 );
    }
}
?>
