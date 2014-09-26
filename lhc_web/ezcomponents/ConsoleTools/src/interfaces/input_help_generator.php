<?php
/**
 * File containing the ezcConsoleInputHelpGenerator interface.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Interface for help generators used in ezcConsoleInput.
 *
 * An instance of a class implementing this interface is used in {@link 
 * ezcConsoleInput} to generate the help information.
 *
 * @package ConsoleTools
 * @version 1.6.1
 *
 * @access private
 * @TODO Verify interface and make it public to replace the validation in 
 *       {@link ezcConsoleInput}.
 */
interface ezcConsoleInputHelpGenerator
{
    /**
     * Creates a new help generator.
     *
     * Creates a new help generator for the given $input.
     * 
     * @param ezcConsoleInput $input
     */
    public function __construct( ezcConsoleInput $input );

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
    public function generateUngroupedOptionHelp( $long = false, array $optionsFilter = null );

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
    public function generateGroupedOptionHelp( array $groups, $long = false, array $optionsFilter = null );

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
    public function generateArgumentHelp( $long = false );

    /**
     * Generates a command line synopsis for the options and arguments.
     *
     * This method generates a synopsis string that lists the options and 
     * parameters available, indicating their usage.
     *
     * @return string
     */
    public function generateSynopsis( array $optionFilter = null );
}

?>
