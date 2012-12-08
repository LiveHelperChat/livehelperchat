<?php
/**
 * File containing the ezcTemplateAutoloaderDefinition class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Contains the definition of an autoloader.
 *
 * It defines the minimum data required for locating and initialising a template
 * autoloader and is used by the template engine to reduce the memory usage when
 * templates does not need compilation.
 *
 * The definition will be turned into a class which implements the
 * ezcTemplateTemplateAutoloader class.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateAutoloaderDefinition extends ezcBaseStruct
{
    /**
     * The path to the PHP file which contains the autoloader class.
     */
    public $path;

    /**
     * The name of the class contained in $path which implements the
     * ezcTemplateAutoloader base class.
     */
    public $className;

    /**
     * Initialises the definition with the path and class name.
     *
     * @param string $path The file path to the loader which is set as $this->path.
     * @param string $className The class name of the loader which is set as
     *                          $this->className.
     */
    public function __construct( $path, $className )
    {
    }

}
?>
