<?php
/**
 * File containing the ezcDocumentWikiPluginToken struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for Wiki plugin token.
 *
 * The most complex token, just contains the full plugin contents. May be post
 * process by the tokenizer to extract its type, parameters and text values.
 * Otherwise it will be ignored, and not handled properly by the parser.
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiPluginToken extends ezcDocumentWikiBlockMarkupToken
{
    /**
     * Plugin type / name.
     *
     * @var string
     */
    public $type;

    /**
     * Plugin parameters
     *
     * @var array
     */
    public $parameters;

    /**
     * Plugin content
     *
     * @var string
     */
    public $text;

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
        $token->type       = $properties['type'];
        $token->parameters = $properties['parameters'];
        $token->text       = $properties['text'];

        return $token;
    }
}

?>
