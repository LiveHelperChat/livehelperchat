<?php
/**
 * File containing the ezcSearchHandler interface.
 *
 * @package Search
 * @version 1.0.9
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Defines interface for all the search backend implementations.
 *
 * @version 1.0.9
 * @package Search
 */
interface ezcSearchHandler
{
    /**
     * Creates a search query object with the fields from the definition filled in.
     *
     * @param string $type
     * @param ezcSearchDocumentDefinition $definition
     * @return ezcSearchFindQuery
     */
    public function createFindQuery( $type, ezcSearchDocumentDefinition $definition );

    /**
     * Builds the search query and returns the parsed response
     *
     * @param ezcSearchFindQuery $query
     * @return ezcSearchResult
     */
    public function find( ezcSearchFindQuery $query );

    /**
     * Finds a document by the document's $id
     *
     * @param mixed $id
     * @param ezcSearchDocumentDefinition $definition
     */
    public function findById( $id, ezcSearchDocumentDefinition $definition );
}
?>
