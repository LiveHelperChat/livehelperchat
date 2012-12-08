<?php
/**
 * File containing the ezcTemplateNoOutputContextException class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception for for missing output contexts in classes.
 *
 * @package Template
 * @version 1.4.2
 */
class ezcTemplateNoOutputContextException extends ezcTemplateException
{
    /**
     * Initialises the exception with the location object $location which
     * contains the locator which is missing.
     *
     * @param string $class The name of the class which is missing the context.
     * @param string $property The name of the property which is missing the contex.
     */
    public function __construct( $class, $property )
    {
        parent::__construct( "The class '{$class}' and property '{$property}' does not contain a template output context which is required." );
    }
}
?>
