<?php
/**
 * File containing the abstract ezcDocumentEzXmlLinkProvider base class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class providing access to links referenced in eZXml documents by url IDs,
 * node IDs or object IDs.
 *
 * @package Document
 * @version 1.3.1
 */
abstract class ezcDocumentEzXmlLinkProvider
{
    /**
     * Fetch URL by ID
     *
     * Fetch and return URL referenced by url_id property.
     *
     * @param string $id
     * @param string $view
     * @param string $show_path
     * @return string
     */
    abstract public function fetchUrlById( $id, $view, $show_path );

    /**
     * Fetch URL by node ID
     *
     * Create and return the URL for a referenced node.
     *
     * @param string $id
     * @param string $view
     * @param string $show_path
     * @return string
     */
    abstract public function fetchUrlByNodeId( $id, $view, $show_path );

    /**
     * Fetch URL by ID
     *
     * Create and return the URL for a referenced object.
     *
     * @param string $id
     * @param string $view
     * @param string $show_path
     * @return string
     */
    abstract public function fetchUrlByObjectId( $id, $view, $show_path );
}

?>
