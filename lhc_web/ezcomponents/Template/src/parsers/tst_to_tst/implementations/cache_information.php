<?php
/**
 * File containing the ezcTemplateFetchCacheInformation class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * Checks if the templates uses some sort of caching.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateFetchCacheInformation extends ezcTemplateTstWalker
{
    public $cacheTst = null;

    public $cacheTemplate = false;
    public $cacheKeys = array();
    public $hasTTL = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function visitCacheTstNode( ezcTemplateCacheTstNode $node )
    {
        $this->cacheTemplate = true;
        $this->cacheTst = $node;

        foreach ( $node->keys as $key )
        {
            $this->cacheKeys[] = $key;
        }

        // And translate the ttl.
        if ( $node->ttl != null ) 
        {
            $this->hasTTL = true;
        }
    }
}
?>
