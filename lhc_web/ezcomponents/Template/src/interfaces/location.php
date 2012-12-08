<?php

/**
 * File containing the ezcTemplateLocationInterface class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Interface for classes implementing a dynamic template location.
 *
 * An object implementing the ezcTemplateLocationInterface can be used as a substitute
 * for the template source in the ezcTemplate::process() method and inside the template
 * {include} block.
 *
 * Inside a template, a custom function is used to create this location object. The 
 * following template source:
 * <code>
 * Hello word!
 * {include dynloc("my_template.ezt")}
 * </code>
 *
 * With the following custom function definition:
 * <code>
 * class DynLocCF implements ezcTemplateCustomFunction
 * {
 *     public static function getCustomFunctionDefinition( $name )
 *     {
 *          if ( $name === "dynloc" )
 *          {
 *             $def = new ezcTemplateCustomFunctionDefinition();
 *             $def->class = __CLASS__;
 *             $def->method = "dynloc";
 *             $def->sendTemplateObject = true;
 *             return $def;
 *          }
 *          return false;
 *     }
 *
 *     public static function dynloc($templateObj, $name)
 *     {
 *         return new DynamicLocation($templateObj, $name);
 *     }
 * }
 * </code>
 * 
 * The dynloc() method returns a new DynamicLocation object. A 
 * simple implementation of the ezcTemplateLocationInterface is shown below:
 *
 * <code>
 * class DynamicLocation implements ezcTemplateLocationInterface
 * {
 *      protected $templatePath;
 *      protected $templateName;
 *
 *      public function __construct( $templateObj, $templateName)
 *      {
 *          $this->templateName = $templateName;
 *          $this->templatePath = $templateObj->usedConfiguration->templatePath;
 *      }
 *
 *      public function getPath()
 *      {
 *          $loc = $this->templatePath ."/". $this->templateName;
 *          if ( !file_exists( $loc ) )
 *          {
 *              $loc = "/fallback/" . $this->templateName;
 *          } 
 *
 *          return $loc;
 *      }
 * }
 * </code>
 *
 * The template will first try to use the original template. If that template 
 * does not exist, it uses the fallback template.
 *
 * @package Template
 * @version 1.4.2
 */
interface ezcTemplateLocation
{
    /**
     * Implement this method to return the path to the template source.
     * The original template name is set with any other method.
     *
     * @return string Path to the template source.
     */
	public function getPath();
}
?>
