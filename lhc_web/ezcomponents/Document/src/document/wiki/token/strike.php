<?php
/**
 * File containing the ezcDocumentWikiStrikeToken struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for Wiki document strike-through inline markup tokens
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiStrikeToken extends ezcDocumentWikiInlineMarkupToken
{
    /**
     * Set state after var_export
     *
     * @param array $properties
     * @return void
     * @ignore
     */
    public static function __set_state( $properties )
    {
        $tokenClass = __CLASS__;
        $token = new $tokenClass(
            $properties['content'],
            $properties['line'],
            $properties['position']
        );

        // Set additional token values
        // $token->value = $properties['value'];

        return $token;
    }
}

?>
