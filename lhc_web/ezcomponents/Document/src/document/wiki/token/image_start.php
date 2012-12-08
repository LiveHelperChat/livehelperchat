<?php
/**
 * File containing the ezcDocumentWikiImageStartToken struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for Wiki document image tag open marker tokens
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentWikiImageStartToken extends ezcDocumentWikiInlineMarkupToken
{
    /**
     * Image width
     *
     * @var int
     */
    public $width      = null;

    /**
     * Image height
     *
     * @var int
     */
    public $height     = null;

    /**
     * Image alignement
     *
     * @var string
     */
    public $alignement = null;

    /**
     * Get image parameter order
     *
     * Images may have any amount of parameters and the order may not be the
     * same for each amount. This method should return an ordered list of
     * parameter names for the given amount of parameters.
     *
     * @param int $count
     * @return array
     */
    public function getImageParameterOrder( $count )
    {
        return array_slice(
            array(
                'resource',
                'title',
            ),
            0, $count
        );
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
        $token->width      = $properties['width'];
        $token->height     = $properties['height'];
        $token->alignement = $properties['alignement'];

        return $token;
    }
}

?>
