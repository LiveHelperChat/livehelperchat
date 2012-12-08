<?php
/**
 * File containing the ezcSearchSimpleImage class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * A sample definition for indexing images.
 *
 * @package Search
 * @version 1.0.9
 */
class ezcSearchSimpleImage implements ezcSearchDefinitionProvider
{
    /**
     * Id for the image.
     *
     * @var string
     */
    public $id;

    /**
     * Image title.
     *
     * @var string
     */
    public $title;

    /**
     * URL for the image.
     *
     * @var string
     */
    public $url;

    /**
     * Image width.
     *
     * @var int
     */
    public $width;

    /**
     * Image height.
     *
     * @var int
     */
    public $height;

    /**
     * Image mime-type.
     *
     * @var string
     */
    public $mime;

    /**
     * Constructs a new image definition.
     *
     * @param string $id
     * @param string $title
     * @param string $url
     * @param int $width
     * @param int $height
     * @param string $mime
     */
    public function __construct( $id = null, $title = null, $url = null, $width = null, $height = null, $mime = null )
    {
        $this->id = $id;
        $this->title = $title;
        $this->url = $url;
        $this->width = $width;
        $this->height = $height;
        $this->mime = $mime;
    }

    /**
     * Returns the definition of this class.
     *
     * @return ezcSearchDocumentDefinition
     */
    static public function getDefinition()
    {
        $n = new ezcSearchDocumentDefinition( 'ezcSearchSimpleImage' );
        $n->idProperty = 'id';
        $n->fields['id']        = new ezcSearchDefinitionDocumentField( 'id', ezcSearchDocumentDefinition::TEXT );
        $n->fields['title']     = new ezcSearchDefinitionDocumentField( 'title', ezcSearchDocumentDefinition::TEXT, 2, true, false, true );
        $n->fields['url']       = new ezcSearchDefinitionDocumentField( 'url', ezcSearchDocumentDefinition::STRING );
        $n->fields['width']     = new ezcSearchDefinitionDocumentField( 'width', ezcSearchDocumentDefinition::INT );
        $n->fields['height']    = new ezcSearchDefinitionDocumentField( 'height', ezcSearchDocumentDefinition::INT );
        $n->fields['mime']      = new ezcSearchDefinitionDocumentField( 'mime', ezcSearchDocumentDefinition::STRING );

        return $n;
    }

    /**
     * Returns the state of this definition as an array.
     *
     * @return array(string=>string)
     */
    public function getState()
    {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'url' => $this->url,
            'width' => $this->width,
            'height' => $this->height,
            'mime' => $this->mime,
        );
    }

    /**
     * Sets the state of this definition.
     *
     * @param array(string=>string) $state
     */
    public function setState( array $state )
    {
        foreach ( $state as $key => $value )
        {
            $this->$key = $value;
        }
    }
}
?>
