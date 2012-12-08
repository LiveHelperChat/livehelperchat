<?php
/**
 * File containing the ezcDocumentWikiTitleToken struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for Wiki document title tokens
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiTitleToken extends ezcDocumentWikiLineMarkupToken
{
    /**
     * Level of title.
     *
     * @var int
     */
    public $level = 0;

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
        $token->level = $properties['level'];

        return $token;
    }
}

?>
