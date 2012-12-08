<?php
/**
 * File containing the ezcConsoleInputStandardHelpGenerator class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */
/**
 * Standard help generator for {@link ezcConsoleInput}.
 *
 * Standard help generation as {@link ezcConsoleInput} did from the start.
 *
 * @package ConsoleTools
 * @version 1.6.1
 *
 * @access private
 * @TODO Verify interface and make it public to replace the validation in 
 *       {@link ezcConsoleInput}.
 */
class ezcConsoleInputStandardHelpGenerator implements ezcConsoleInputHelpGenerator
{
    /**
     * Input object. 
     * 
     * @var ezcConsoleInput
     */
    private $input;

    /**
     * Creates a new help generator.
     *
     * Creates a new help generator for the given $input.
     * 
     * @param ezcConsoleInput $input
     */
    public function __construct( ezcConsoleInput $input )
    {
        $this->input = $input;
    }

    /**
     * Generates help information as a multidimensional array.
     *
     * This method generates a tabular view on the help information of a 
     * program. The returned array has the following structure:
     *
     * <code>
     * <?php
     * array(
     *  0 => array(
     *      0 => '<option short/long name>',
     *      1 => '<option help, depending on the $long parameter>'
     *  ),
     *  1 => array(
     *      0 => '<option short name> / <option long name>',
     *      1 => '<option help, depending on the $long parameter>'
     *  ),
     *  // ...
     * );
     * ?>
     * </code>
     *
     * Each row of the array represents the help information for a single option.
     * The first cell of a row contains the option name (maybe short, long or 
     * both), the second cell contains the help text of the option.
     *
     * The returned array is used by {@link ezcConsoleInput} for different 
     * purposes.
     * For example, the user can retrieve it raw through the
     * {@link ezcConsoleInput::getHelp()} method, he can generate a help
     * {@link ezcConsoleTable} through {@link ezcConsoleInput::getHelpTable()} 
     * are can generate a printable help text through {@link 
     * ezcConsoleInput::getHelpText()}.
     *
     * The parameter $long defines if the long or short help text of the 
     * options should be used in the second cell of the returned array. The
     * $optionsFilter parameter is used to restrict the generated help to a certain 
     * sub-set of options. It consists of an array of short or long names of 
     * the options to include.
     * 
     * @param bool $long 
     * @param array(string) $optionsFilter
     * @return array(array(string))
     */
    public function generateUngroupedOptionHelp( $long = false, array $optionsFilter = null )
    {
        $help = array();
        foreach ( $this->input->getOptions() as $id => $param )
        {
            if ( $optionsFilter === null || in_array( $param->short, $optionsFilter ) || in_array( $param->long, $optionsFilter ) )
            {
                $help[] = $this->getOptionHelpRow( $long, $param );
            }
        }
        return $help;
    }

    /**
     * Generates help information as a multidimensional array, grouped in categories.
     *
     * This method behaves similar to {@link generateUngroupedOptionHelp()}. In 
     * contrast to the latter one, this method returns an array with 1 
     * dimension more, grouping options into categories. The $groups parameter 
     * defines the categories to generate. Each category may contain an 
     * arbitrary number of options, options might occur in different 
     * categories.
     *
     * The returned array has the follorwing format:
     *
     * <code>
     * <?php
     * array(
     *  '<category name>' => array(
     *      0 => array(
     *          0 => '<option short/long name>',
     *          1 => '<option help, depending on the $long parameter>'
     *      ),
     *      1 => array(
     *          0 => '<option short name> / <option long name>',
     *          1 => '<option help, depending on the $long parameter>'
     *      ),
     *      // ...
     *  ),
     *  '<category name>' => array(
     *      // ...
     *  ),
     *  // ...
     * );
     * ?>
     * </code>
     *
     * The $long parameter, as in {@link generateUngroupedOptionHelp()} 
     * determines if the options short or long help is to be used. The
     * $params array can in addition be used to determine if a parameter
     * is displayed at all. If $optionsFilter is submitted and is not null, 
     * only options listed in it will be shown in the help at all.
     * 
     * @param array(string=>array(string)) $groups
     * @param bool $long 
     * @param array(string) $params 
     * @return array(string=>array(array(string)))
     */
    public function generateGroupedOptionHelp( array $groups, $long = false, array $optionsFilter = null )
    {
        $help = array();
        foreach ( $groups as $groupName => $groupOptions )
        {
            foreach ( $groupOptions as $optionName )
            {
                $option = $this->input->getOption( $optionName );
                if ( $optionsFilter === null || in_array( $option->short, $optionsFilter ) || in_array( $option->long, $optionsFilter ) )
                {
                    $help[$groupName][] = $this->getOptionHelpRow(
                        $long,
                        $option
                    );
                }
            }
        }
        return $help;
    }

    /**
     * Generates help information as a multi-dimensonal array for the given $argumentDefinition.
     *
     * This method generates a tabular help information for the given 
     * $argumentDefinition in the following format:
     *
     * <code>
     * <?php
     * array(
     *  0 => array(
     *      0 => '<argument synopsis>',
     *      1 => '<argument help text>'
     *  ),
     *  1 => array(
     *      0 => '<argument synopsis>',
     *      1 => '<argument help text>'
     *  ),
     *  // ...
     * )
     * ?>
     * </code>
     * 
     * The $long parameter defines if the long of short help text should be 
     * used.
     *
     * @param bool $long
     * @return array(array(string))
     */
    public function generateArgumentHelp( $long = false )
    {
        $help = array();
        if ( $this->input->argumentDefinition !== null )
        {
            foreach ( $this->input->argumentDefinition as $arg )
            {
                $argSynopsis = "<%s:%s>";
                switch ( $arg->type )
                {
                    case ezcConsoleInput::TYPE_INT:
                        $type = "int";
                        break;
                    case ezcConsoleInput::TYPE_STRING:
                        $type = "string";
                        break;
                }
                $argSynopsis = sprintf( $argSynopsis, $type, $arg->name );
                $help[] = ( $long === true )
                        ? array( 
                            $argSynopsis,
                            $arg->longhelp . ( $arg->mandatory === false 
                                               ? ' (optional' . ( $arg->default !== null 
                                                                  ? ', default = ' . ( is_array( $arg->default ) 
                                                                                       ? "'" . implode( "' '", $arg->default ) . "'" 
                                                                                       : "'$arg->default'" 
                                                                                     )
                                                                  : '' 
                                                                ) . ')'
                                               : ''
                                             )
                          )
                        : array( $argSynopsis, $arg->shorthelp );
            }
        }
        return $help;
    }

    /**
     * Creates 1 text row for displaying options help. 
     *
     * Returns a single array entry for the {@link getOptionHelpRow()} method.
     *
     * @param bool $long 
     * @param ezcConsoleOption $param
     * @return string
     */
    private function getOptionHelpRow( $long, ezcConsoleOption $param )
    {
        return array( 
            ( $param->short !== "" ? '-' . $param->short . ' / ' : "" ) . '--' . $param->long,
            $long == false ? $param->shorthelp : $param->longhelp,
        );
    }

    /**
     * Generates a command line synopsis for the options and arguments.
     *
     * This method generates a synopsis string that lists the options and 
     * parameters available, indicating their usage. If $optionsFilter is
     * submitted, only the options named in this array (short or long variant) 
     * will be included in the synopsis.
     *
     * @param array(string) $optionsFilter
     * @return string
     */
    public function generateSynopsis( array $optionFilter = null )
    {
        $usedOptions = array( 'short' => array(), 'long' => array() );
        $allowsArgs = true;
        $synopsis = '$ ' . ( isset( $argv ) && sizeof( $argv ) > 0 ? $argv[0] : $_SERVER['argv'][0] ) . ' ';
        foreach ( $this->input->getOptions() as $option )
        {
            if ( $optionFilter === null || in_array( $option->short, $optionFilter ) ||  in_array( $option->long, $optionFilter ) )
            {
                $synopsis .= $this->createOptionSynopsis( $option, $usedOptions, $allowsArgs );
            }
        }
        if ( $this->input->argumentDefinition === null )
        {
            // Old handling
            $synopsis .= " [[--] <args>]";
        }
        else
        {
            $synopsis .= "[--] " . $this->createArgumentsSynopsis();
        }
        return $synopsis;
    }

    /**
     * Returns the synopsis string for a single option and its dependencies.
     *
     * This method returns a part of the program synopsis, specifically for a
     * certain parameter. The method recursively adds depending parameters up
     * to the 2nd depth level to the synopsis. The second parameter is used
     * to store the short names of all options that have already been used in 
     * the synopsis (to avoid adding an option twice). The 3rd parameter 
     * determines the actual deps in the option dependency recursion to 
     * terminate that after 2 recursions.
     * 
     * @param ezcConsoleOption $option        The option to include.
     * @param array(string) $usedOptions Array of used option short names.
     * @param int $depth                      Current recursion depth.
     * @return string The synopsis for this parameter.
     */
    private function createOptionSynopsis( ezcConsoleOption $option, &$usedOptions, $depth = 0 )
    {
        $synopsis = '';

        // Break after a nesting level of 2
        if ( $depth++ > 2 || ( in_array( $option->short, $usedOptions['short'] ) && in_array( $option->long, $usedOptions['long'] ) ) ) return $synopsis;
        
        $usedOptions['short'][] = $option->short;
        $usedOptions['long'][]  = $option->long;
        
        $synopsis .= $option->short !== "" ? "-{$option->short}" : "--{$option->long}";

        if ( isset( $option->default ) )
        {
            $synopsis .= " " . ( $option->type === ezcConsoleInput::TYPE_STRING ? '"' : '' ) . $option->default . ( $option->type === ezcConsoleInput::TYPE_STRING ? '"' : '' );
        }
        else if ( $option->type !== ezcConsoleInput::TYPE_NONE )
        {
            $synopsis .= " ";
            switch ( $option->type )
            {
                case ezcConsoleInput::TYPE_STRING:
                    $synopsis .= "<string>";
                    break;
                case ezcConsoleInput::TYPE_INT:
                    $synopsis .= "<int>";
                    break;
            }
        }

        foreach ( $option->getDependencies() as $rule )
        {
            $deeperSynopsis = $this->createOptionSynopsis( $rule->option, $usedOptions, $depth );
            $synopsis .= ( iconv_strlen( trim( $deeperSynopsis ), 'UTF-8' ) > 0 
                ? ' ' . $deeperSynopsis
                : ''
            );
        }
        
        if ( $option->arguments === false )
        {
            $allowsArgs = false;
        }
        
        // Make the whole thing optional?
        if ( $option->mandatory === false )
        {
            $synopsis = "[$synopsis]";
        }

        return $synopsis . ' ';
    }

    /**
     * Generate synopsis for arguments. 
     * 
     * @return string The synopsis string.
     */
    private function createArgumentsSynopsis()
    {
        $mandatory = true;
        $synopsises = array();
        foreach ( $this->input->argumentDefinition as $arg )
        {
            $argSynopsis = "";
            if ( $arg->mandatory === false )
            {
                $mandatory = false;
            }
            $argSynopsis .= "<%s:%s>";
            switch ( $arg->type )
            {
                case ezcConsoleInput::TYPE_INT:
                    $type = "int";
                    break;
                case ezcConsoleInput::TYPE_STRING:
                    $type = "string";
                    break;
            }
            $argSynopsis = sprintf( $argSynopsis, $type, $arg->name );
            $synopsises[] = $mandatory === false ? "[$argSynopsis]" : $argSynopsis;
            if ( $arg->multiple === true )
            {
                $synopsises[] = "[$argSynopsis ...]";
                break;
            }
        }
        return implode( " ", $synopsises );
    }
}

?>
