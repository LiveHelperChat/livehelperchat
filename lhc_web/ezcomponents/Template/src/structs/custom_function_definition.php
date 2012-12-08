<?php
/**
 * File containing the ezcTemplateCustomFunctionDefinition class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Contains the definition of a custom function.
 *
 * Example of use: create a function to hide a mail address.
 *
 * 1. Create a class which implements ezcTemplateCustomFunction and which
 * will be included in your application (with the autoloading mechanism).
 * <code>
 * class htmlFunctions implements ezcTemplateCustomFunction
 * {
 *     public static function getCustomFunctionDefinition( $name )
 *     {
 *         switch ($name )
 *         {
 *             case "hide_mail":
 *                 $def = new ezcTemplateCustomFunctionDefinition();
 *                 $def->class = __CLASS__;
 *                 $def->method = "hide_mail";
 *                 $def->parameters = array( "mailAddress" );
 *                 return $def;
 *         }
 *         return false;
 *     }
 *
 *     public static function hide_mail( $mailAddress )
 *     {
 *         $old = array( '@', '.' );
 *         $new = array( ' at ', ' dot ' );
 *         return  str_replace( $old, $new, $mailAddress );
 *     }
 * }
 * </code>
 *
 * 2. Assign the class to the Template configuration in your application.
 * <code>
 * $config = ezcTemplateConfiguration::getInstance();
 * $config->addExtension( "htmlFunctions" );
 * </code>
 *
 * 3. Use the custom function in the template.
 * <code>
 * {hide_mail( "john.doe@example.com" )}
 * </code>
 * The generated html code for this will be: john dot doe at example dot com
 *
 * @package Template
 * @version 1.4.2
 * @mainclass
 */
class ezcTemplateCustomFunctionDefinition extends ezcTemplateCustomExtension
{
    /**
     * Holds the (static) class that implements the function to be executed.
     *
     * @var string
     */
    public $class;

    /**
     * Holds the (static) method that should be run.
     *
     * @var string
     */
    public $method;

    /**
     * Holds the required and optional named parameters for this custom function.
     *
     * The optional parameters should be specified after the required parameters.
     * - Required parameters are named strings.
     * - Optional parameters are named strings enclosed with square brackets.
     *
     * @var array(string)
     */
    public $parameters = array();

    /**
     * Whether or not the Template object is available in the custom function.
     *
     * Be aware that if you change this, your custom function's signature
     * changes as the first argument will then be the template object.
     *
     * @var bool
     */
    public $sendTemplateObject = false;


    /**
     * Whether or not the custom function can have an undefined amount of parameters.
     * The maximum amount of parameters check will be omitted. The custom function
     * implementation will most probably use PHP function: func_get_args() or simular.
     *
     * @var bool
     */
    public $variableArgumentList = false;
}
?>
