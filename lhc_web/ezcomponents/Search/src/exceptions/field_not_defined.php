<?php
/**
 * File containing the ezcSearchFieldNotDefinedException class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown when a field name is used that has not been defined
 * through the document definition.
 *
 * @package Search
 * @version 1.0.9
 */
class ezcSearchFieldNotDefinedException extends ezcSearchException
{
    /**
     * Constructs an ezcSearchFieldNotDefinedException for document type $type
     * and field $field.
     *
     * @param string $type
     * @param string $field
     */
    public function __construct( $type, $field )
    {
        $message = "The document type '$type' does not define the field '$field'.";
        parent::__construct( $message );
    }
}
?>
