<?php
/**
 * File containing the ezcDocumentPcssDeclarationDirective class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Pdf CSS layout directive.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentPcssDeclarationDirective extends ezcDocumentPcssDirective
{
    /**
     * Return the type of the directive
     *
     * @return string
     */
    public function getType()
    {
        return strtolower( substr( $this->address, 1 ) );
    }

    /**
     * Recreate directive from var_export
     *
     * @param array $properties
     * @return ezcDocumentPcssDirective
     */
    public static function __set_state( $properties )
    {
        return new ezcDocumentPcssDeclarationDirective(
            $properties['address'],
            $properties['formats'],
            $properties['file'],
            $properties['line'],
            $properties['position']
        );
    }
}
?>
