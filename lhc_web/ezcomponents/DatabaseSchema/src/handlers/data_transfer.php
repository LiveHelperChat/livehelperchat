<?php
/**
 * File containing the ezcDbSchemaHandlerDataTransfer interface.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Bulk data transfer functionality.
 *
 * This interface declares methods common for handlers that support
 * bulk data transfers.
 *
 * <b>Description</b>:
 *
 * There are two handler participating in bulk data transfer: source and destination.
 * Each of the handlers must implement ezcDbSchemaHandlerDataTransfer interface.
 *
 * The source handler implements transfer() method.
 * This method transfers all the tables in storage one-by-one.
 *
 * For each of the tables we transfer data of, the source handler calls
 * setTableBeingTransferred() method on the destination handler.
 *
 * For each row being transferred, the source handler calls saveRow() method on the destination handler.
 *
 * Besides, when the transfer starts, we call destination handler's openTransferDestination() method,
 * and when the transfer finishes, we call destination handler's closeTransferDestination() method.
 *
 * Here is a typical implementation of transfer() method:
 *
 * <code>
 * class SomeSchemaHandler
 * {
 *     public function transfer(  $storage, $storageType, $dstHandler )
 *     {
 *         $tables = $this->getTablesList();

 *         foreach ( $tables as $tableName )
 *         {
 *             $tableFields = $this->getTableFields( $tableName );
 *             $dstHandler->setTableBeingTransferred( $tableName, $tableFields );
 *
 *             $tableData = $this->getTableData( $tableName );
 *             foreach ( $tableData as $row )
 *                 $dstHandler->saveRow( $row );
 *         }
 *     }
 * }
 *
 * </code>
 *
 * The destination handler should implement the following methods:
 * - openTransferDestination()
 * - setTableBeingTransferred()
 * - saveRow()
 * - closeTransferDestination()
 *
 * If you want your handler to be able to act both as source and destination
 * for bulk data transfers, then you should implement all the interface's
 * methods in the handler.
 *
 * @package DatabaseSchema
 */

interface ezcDbSchemaHandlerDataTransfer
{
    /**
     * Actually transfer data [source].
     */
    public function transfer( $storage, $storageType, $dstHandler );

    /**
     * Prepare destination handler for transfer [destination].
     */
    public function openTransferDestination( $storage, $storageType );

    /**
     * Tell destination handler that there is no more data to transfer. [destination]
     */
    public function closeTransferDestination();

    /**
     * Start to transfer data of the next table. [destination]
     */
    public function setTableBeingTransferred( $tableName, $tableFields = null );

    /**
     *  Save given row. [destination]
     */
    public function saveRow( $row );
}
?>
