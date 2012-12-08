<?php
/**
 * File containing the ezcTemplateCustomBlockDefinition class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Contains the definition of a custom block.
 *
 * Example of use: create a link custom block.
 *
 * 1. Create a class which implements ezcTemplateCustomBlock and which
 * will be included in your application (with the autoloading mechanism).
 * <code>
 * class htmlBlocks implements ezcTemplateCustomBlock
 * {
 *     public static function getCustomBlockDefinition( $name )
 *     {
 *         switch ( $name )
 *         {
 *             case "link":
 *                 $def = new ezcTemplateCustomBlockDefinition();
 *                 $def->class = __CLASS__;
 *                 $def->method = 'link';
 *                 $def->hasCloseTag = false;
 *                 $def->startExpressionName = 'from';
 *                 $def->requiredParameters = array( 'title' );
 *                 $def->optionalParameters = array( 'from', 'to' );
 *                 return $def;
 *         }
 *         return false;
 *     }
 *
 *     public static function link( $parameters )
 *     {
 *         $title = "";
 *         if ( isset( $parameters['title'] ) )
 *         {
 *             $title = "title=\"{$parameters['title']}\"";
 *         }
 *         return "<" . "a href=\"{$parameters['to']}\" {$title}>{$parameters['from']}</a>";
 *     }
 * }
 * </code>
 *
 * 2. Assign the class to the Template configuration in your application.
 * <code>
 * $config = ezcTemplateConfiguration::getInstance();
 * $config->addExtension( "htmlBlocks" );
 * </code>
 *
 * 3. Use the custom block in the template.
 * <code>
 * {link "Google" to "http://www.google.com" title "Google search engine"}
 * </code>
 * The generated html code for this will be a hyperlink.
 *
 * @package Template
 * @version 1.4.2
 * @mainclass
 */
class ezcTemplateCustomBlockDefinition extends ezcTemplateCustomExtension
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
     * Specifies whether the class has an open and close tag or only a open tag.
     *
     * @var bool
     */
    public $hasCloseTag;

    /**
     * Holds the first parameter of a custom block without a name.
     *
     * If the custom block should have a start expression then this variable
     * specifies a name for it. The name should reappear in either the
     * {@link optionalParameters} or the {@link requiredParameters}.
     *
     * @var string
     */
    public $startExpressionName;

    /**
     * Holds the optional named parameters for this custom block.
     *
     * @var array(string)
     */
    public $optionalParameters = array();

    /**
     * Holds the required named parameters for this custom block.
     *
     * @var array(string)
     */
    public $requiredParameters = array();


    public $isStatic = false;

    /**
     * Whether or not the Template object is available in the custom block.
     *
     * Be aware that if you change this, your custom block's signature
     * changes as the first argument will then be the template object.
     *
     * @var bool
     */
    public $sendTemplateObject = false;

    /**
     * When excessParameters is set to true, the custom block accepts any amount of 
     * parameters over the required parameters. 
     *
     * @var bool
     */
    public $excessParameters = false;

}
?>
