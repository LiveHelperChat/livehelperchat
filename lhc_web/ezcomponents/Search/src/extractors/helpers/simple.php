<?php
/**
 * File containing the ezcSearchSimpleArticle class.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * A sample definition for indexing articles.
 *
 * @package Search
 * @version 1.0.9
 */
class ezcSearchSimpleArticle implements ezcSearchDefinitionProvider, ezcBasePersistable
{
    /**
     * Id for the article.
     *
     * @var string
     */
    public $id;

    /**
     * Article title.
     *
     * @var string
     */
    public $title;

    /**
     * Article body.
     *
     * @var string
     */
    public $body;

    /**
     * Published date for the article.
     *
     * @var DateTime
     */
    public $published;

    /**
     * URL for the article.
     *
     * @var string
     */
    public $url;

    /**
     * Article type.
     *
     * @var string
     */
    public $type;

    /**
     * Constructs a new image definition.
     *
     * @param string $id
     * @param string $title
     * @param string $body
     * @param DateTime $published
     * @param string $url
     * @param string $type
     */
    public function __construct( $id = null, $title = null, $body = null, $published = null, $url = null, $type = null )
    {
        $this->id = $id;
        $this->title = $title;
        $this->body = $body;
        $this->published = $published;
        $this->url = $url;
        $this->type = $type;
    }

    /**
     * Returns the definition of this class.
     *
     * @return ezcSearchDocumentDefinition
     */
    static public function getDefinition()
    {
        $n = new ezcSearchDocumentDefinition( 'ezcSearchSimpleArticle' );
        $n->idProperty = 'id';
        $n->fields['id']        = new ezcSearchDefinitionDocumentField( 'id', ezcSearchDocumentDefinition::TEXT );
        $n->fields['title']     = new ezcSearchDefinitionDocumentField( 'title', ezcSearchDocumentDefinition::TEXT, 2, true, false, true );
        $n->fields['body']      = new ezcSearchDefinitionDocumentField( 'body', ezcSearchDocumentDefinition::TEXT, 1, false, false, true );
        $n->fields['published'] = new ezcSearchDefinitionDocumentField( 'published', ezcSearchDocumentDefinition::DATE );
        $n->fields['url']       = new ezcSearchDefinitionDocumentField( 'url', ezcSearchDocumentDefinition::STRING );
        $n->fields['type']      = new ezcSearchDefinitionDocumentField( 'type', ezcSearchDocumentDefinition::STRING, 0, true, false, false );

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
            'body' => $this->body,
            'published' => $this->published,
            'url' => $this->url,
            'type' => $this->type,
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
