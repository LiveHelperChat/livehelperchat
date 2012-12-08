<?php
/**
 * File containing the ezcTemplateTranslationExtractor class.
 *
 * @package TemplateTranslationTiein
 * @version 1.1.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcTemplateTranslationExtractor is capable of creating or updating
 * translation files by extracting translatable strings from templates.
 *
 * To run the extractor, please use the file "runextractor.php", inside this
 * package.
 *
 * To extract new translatable strings from a template file, use the following
 * synopsis:
 *
 * <code>
 * $ php TemplateTranslationTiein/src/runextractor.php -t path/to/template/files/ path/to/translation/files/
 * </code>
 *
 * The -t / --templates parameter points to the template files. The -o /
 * --overwrite option specifies if existing files should be overwritten instead
 * of being updated.  The argument for the program specifies the directory,
 * where the translation files will be stored or updated.
 *
 * For help information simply call
 * <code>
 * $ php TemplateTranslationTiein/src/runextractor.php
 * </code>
 * or
 * <code>
 * $ php TemplateTranslationTiein/src/runextractor.php -h
 * </code>
 * for extended help information.
 *
 * @package TemplateTranslationTiein
 * @version 1.1.1
 */
class ezcTemplateTranslationExtractor
{
    /**
     * Program description for help text.
     *
     */
    const PROGRAM_DESCRIPTION = 'Extracts translatable strings from eZ Components template files. The directory to save the translation files to is provided as an argument.';

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

    /**
     * Translation backend to store the output to.
     *
     * @var ezcTranslationBackend
     */
    private $backend;

    /**
     * Create a new generator.
     * This method initializes the necessary objects to run the application.
     */
    public function __construct()
    {
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
                "t",        // short
                "templates",   // long
                ezcConsoleInput::TYPE_STRING,
                null,       // default
                false,      // multiple
                "Template directory to search for translatable strings.",
                "A directory with eZ Components template files. It is searched recursivly for new translatable strings.",
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
                '[LOCALE].xml',       // default
                false,      // multiple
                "File name format of translation file.",
                "The file format the generated translation files should get. The string \"[LOCALE]\" is automatically replaced with the locale name.",
                array(),    // dependencies
                array(),    // exclusions
                true,       // arguments
                false       // mandatory
            )
        );

        $this->input->registerOption(
            new ezcConsoleOption(
                "l",        // short
                "locale",   // long
                ezcConsoleInput::TYPE_STRING,
                'en',       // default
                false,      // multiple
                "Template locale.",
                "The locale of the strings within the templates.",
                array(),    // dependencies
                array(),    // exclusions
                true,       // arguments
                false       // mandatory
            )
        );

        $this->input->registerOption(
            new ezcConsoleOption(
                "o",        // short
                "overwrite",// long
                ezcConsoleInput::TYPE_NONE,
                null,       // default
                false,      // multiple
                "Overwrite existing files.",
                "If this option is set, files will be overwriten if they already exist.",
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

        $this->input->argumentDefinition = new ezcConsoleArguments();

        $this->input->argumentDefinition[0] = new ezcConsoleArgument( "translation dir" );
        $this->input->argumentDefinition[0]->shorthelp = "Translation file directory.";
        $this->input->argumentDefinition[0]->longhelp  = "Directory where translation files will be stored.";

        $this->output->outputLine( 'eZ components extractor for translatable strings in templates.', 'info' );
        $this->output->outputLine();
    }

    /**
     * Run the generator.
     * Process the given options and generates translation files.
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
                    self::PROGRAM_DESCRIPTION,
                    80,
                    true
                ),
                "help"
            );
            exit( 0 );
        }

        $translationDir = $this->input->argumentDefinition["translation dir"]->value;
        $templatePath = realpath( $this->input->getOption( "templates" )->value );

        // setup the backend to write to and/or read from
        $options = array (
            'format' => $this->input->getOption( "format" )->value,
			'keepObsolete' => true
        );
        $this->backend = new ezcTranslationTsBackend( $translationDir );
        $this->backend->setOptions( $options );

        $locale = $this->input->getOption( "locale" )->value;

        // remove the translation file if option overwrite is set
        $translationFile = $this->backend->buildTranslationFileName( $locale );
        if ( $this->input->getOption( "overwrite" )->value === true && file_exists( $translationFile ) )
        {
            unlink( $translationFile );
        }

        // init the writer
        $this->backend->initWriter( $locale );

        // keep track of which contexts are in use
        $usedContexts = array();

        // find the .ezt files and loop over them.
        $it = ezcBaseFile::findRecursive( $templatePath, array( '@\.ezt$@' ) );
        foreach ( $it as $item )
        {
            $pathname = $this->unifyFilepath( realpath( $item ), $templatePath );
            $this->output->outputLine( 'Processing file ' . $pathname . ' ...' );

            // get translation contexts from template
            $contexts = $this->getTranslationsFromTemplate( $item );
            foreach ( $contexts as $contextName => $translationMapNew )
            {
                // record that this context is in use
                $usedContexts[] = $contextName;

                // get existing translation strings from file
                if ( $this->input->getOption( "overwrite" )->value === false )
                {
                    $translationMapOriginal = $this->getTranslationsFromTsFile( $contextName );
                }
                else
                {
                    $translationMapOriginal = array();
                }

                // create empty context
                $context = array();
                // insert new translations
                foreach ( $translationMapNew as $original => $translationElement )
                {
                    // insert new strings
                    if ( !isset( $translationMapOriginal[$original] ) )
                    {
                        // edit filename to unify accross platforms and strip template path
                        $translationElement->filename = $this->unifyFilepath( $translationElement->filename, $templatePath );
                        $context[] = $translationElement;
                    }
                }
                // update translations
                if ( $this->input->getOption( "overwrite" )->value === false )
                {
                    foreach ( $translationMapOriginal as $original => $translationElement )
                    {
                        // update data
                        if ( isset( $translationMapNew[$original] ) )
                        {
                            $new = $translationMapNew[$original];
                            $translationElement->comment = $new->comment;
                            $translationElement->filename = $this->unifyFilepath( $new->filename, $templatePath );
                            $translationElement->line = $new->line;
                            $translationElement->column = $new->column;
                            // change status of previously obsolete strings
                            if ( $translationElement->status == ezcTranslationData::OBSOLETE )
                            {
                                if ( !empty( $translationElement->translation ) )
                                {
                                    $translationElement->status = ezcTranslationData::TRANSLATED;
                                }
                                else
                                {
                                    $translationElement->status = ezcTranslationData::UNFINISHED;
                                }
                            }
                            $context[] = $translationElement;
                        }
                        // flag obsolete strings (only if filename is equal, because the context
                        // could be used in different files)
                        elseif ( $translationElement->filename == $pathname )
                        {
                            $translationElement->status = ezcTranslationData::OBSOLETE;
                            $context[] = $translationElement;
                        }
                    }
                }
                $this->backend->storeContext( $contextName, $context );
            }
        }

        // write translation data to file
        $this->backend->deinitWriter();

        $contextNames = $this->backend->getContextNames( $locale );

        // init the writer
        $this->backend->initWriter( $locale );

        // check which contexts are now totally gone
        foreach ( $contextNames as $contextName )
        {
            if ( !in_array( $contextName, $usedContexts ) )
            {
                echo "$contextName is all obsolete\n";
                $context = $this->backend->getContext( $locale, $contextName );
                foreach ( $context as &$translationElement )
                {
                    $translationElement->status = ezcTranslationData::OBSOLETE;
                }
                $this->backend->storeContext( $contextName, $context );
            }
        }

        // write translation data to file
        $this->backend->deinitWriter();

        $this->output->outputLine();
        $this->output->outputLine( "Translation file for locale '{$locale}' successfully written to '{$translationDir}'.", 'info' );
    }

    /**
     * Get translation array with translation data for each context from the
     * template.
     *
     * @param string $filename
     * @return array(string=>array(ezcTranslationData))
     */
    function getTranslationsFromTemplate( $filename )
    {
        $source = new ezcTemplateSourceCode( $filename, $filename );
        $source->load();

        $parser = new ezcTemplateParser( $source, new ezcTemplate() );
        $tst = $parser->parseIntoNodeTree();

        $et = new ezcTemplateTranslationStringExtracter( $parser );
        $eted = $tst->accept( $et );

        return $et->getStrings();
    }

    /**
     * Get translations for given context.
     *
     * @param string $contextName
     * @return array(string=>ezcTranslationData)
     */
    function getTranslationsFromTsFile( $contextName )
    {
        $translationMapOriginal = array();
        try
        {
            // get the original context
            $context = $this->backend->getContext( $this->input->getOption( "locale" )->value, $contextName );
            // store all existing translations in associative array
            foreach ( $context as $translationElement )
            {
                $translationMapOriginal[$translationElement->original] = $translationElement;
            }
        }
        catch ( ezcTranslationMissingTranslationFileException $e )
        {
            // no ts file existing yet
        }
        catch ( ezcTranslationContextNotAvailableException $e )
        {
            // no context of this name existing yet
        }
        return $translationMapOriginal;
    }

    /**
     * Unifies directory seperator accross platforms and strips path to the template dir
     * to get the possibility to run this script from different template locations.
     *
     * @param string $pathname
     * @param string $templatePath
     * @return string
     */
    function unifyFilepath( $pathname, $templatePath )
    {
        $file = str_replace( '\\', '/', substr( $pathname, strlen( $templatePath ) ) );
        return $file[0] === '/' ? substr( $file, 1 ) : $file;
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
                    self::PROGRAM_DESCRIPTION
                ),
                "help"
            );
        }
        exit( -1 );
    }
}
?>
