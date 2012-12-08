<?php
/**
 * File containing the ezcTemplateNoManagerException class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception for for missing managers in classes.
 *
 * @package Template
 * @version 1.4.2
 */
class ezcTemplateNoManagerException extends ezcTemplateException
{
    /**
     * Initialises the exception with the location object $location which
     * contains the locator which is missing.
     *
     * @param string $class The name of the class which is missing the manager.
     * @param string $property The name of the property which is missing the manager.
     */
    public function __construct( $class, $property )
    {
        parent::__construct( "The class '{$class}' and property '{$property}' does not contain a template manager which is required." );
    }
}
?>
