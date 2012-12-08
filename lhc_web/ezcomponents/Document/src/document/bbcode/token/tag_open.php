<?php
/**
 * File containing the ezcDocumentBBCodeInlineMarkupToken struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for BBCode document nline markup tokens
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentBBCodeTagOpenToken extends ezcDocumentBBCodeToken
{
    /**
     * Opening Token parameters
     * 
     * @var string
     */
    public $parameters;

    /**
     * Construct BBCode token
     *
     * @ignore
     * @param string $content
     * @param int $line
     * @param int $position
     * @return void
     */
    public function __construct( $content, $line, $position = 0 )
    {
        if ( strpos( $content, '=' ) )
        {
            list( $content, $parameters ) = explode( '=', $content, 2 );
            $this->parameters = $parameters;
        }

        parent::__construct( $content, $line, $position );
    }

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
        $token->parameters = $properties['parameters'];

        return $token;
    }
}

?>
