<?php
/**
 * File containing the ezcDocumentBBCodeToken struct
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Struct for BBCode document document tokens
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentBBCodeToken extends ezcBaseStruct
{
    /**
     * Token content
     *
     * @var mixed
     */
    public $content;

    /**
     * Line of the token in the source file
     *
     * @var int
     */
    public $line;

    /**
     * Position of the token in its line.
     *
     * @var int
     */
    public $position;

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
        $this->content  = $content;
        $this->line     = $line;
        $this->position = $position;
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
        return null;
    }
}

?>
