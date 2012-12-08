<?php
/**
 * File containing the ezcDocumentOdtFormattingPropertiesExistException class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown if formatting properties of the same type are set twice in 
 * an {@link ezcDocumentOdtFormattingPropertyCollection}.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentOdtFormattingPropertiesExistException extends ezcDocumentException
{
    /**
     * Creates a new exception for the given $properties.
     * 
     * @param ezcDocumentOdtFormattingProperties $properties 
     */
    public function __construct( ezcDocumentOdtFormattingProperties $properties )
    {
        parent::__construct(
            "Formatting properties of type '{$properties->type}' are already set."
        );
    }
}

?>
