<?php
/**
 * File containing the ezcSearchIndexHandler interface.
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
interface ezcSearchIndexHandler
{
    /**
     * Starts a transaction for indexing.
     *
     * When using a transaction, the amount of processing that the search
     * backend does decreases, increasing indexing performance. Without this,
     * the component sends a commit after every document that is indexed.
     * Transactions can be nested, when commit() is called the same number of
     * times as beginTransaction(), the component sends a commit.
     */
    public function beginTransaction();

    /**
     * Ends a transaction and calls commit.
     *
     * @throws ezcSearchTransactionException if no transaction is active.
     */
    public function commit();

    /**
     * Indexes the document $document using definition $definition
     *
     * @param ezcSearchDocumentDefinition $definition
     * @param mixed $document
     */
    public function index( ezcSearchDocumentDefinition $definition, $document );

    /**
     * Creates a delete query object with the fields from the definition filled in.
     *
     * @param string $type
     * @param ezcSearchDocumentDefinition $definition
     * @return ezcSearchDeleteQuery
     */
    public function createDeleteQuery( $type, ezcSearchDocumentDefinition $definition );

    /**
     * Builds the delete query and returns the parsed response
     *
     * @param ezcSearchDeleteQuery $query
     */
    public function delete( ezcSearchDeleteQuery $query );

    /**
     * Deletes a document by the document's $id
     *
     * @param mixed $id
     * @param ezcSearchDocumentDefinition $definition
     */
    public function deleteById( $id, ezcSearchDocumentDefinition $definition );
}
?>
