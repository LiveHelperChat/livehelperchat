<?php
/**
 * File containing the ezcDbUtilities class.
 *
 * @package Database
 * @version 1.4.7
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Various database methods.
 *
 * This implementation is oracle specific.
 *
 * This class inherits most of its database handling functionality from
 * PDO ({@link http://php.net/PDO}) -- an object-oriented database abstraction
 * layer that is going to become a new standard for PHP applications.
 *
 * @package Database
 * @todo this class must be renamed
 * @access private
 * @version 1.4.7
 */
class ezcDbUtilitiesOracle extends ezcDbUtilities
{
    /**
     * Constructs a new db util using the db handler $db.
     *
     * @param ezcDbHandler $db
     */
    public function __construct( $db )
    {
        parent::__construct( $db );
    }

    /**
     * Remove all tables from the database.
     */
    public function cleanup()
    {
        $this->db->beginTransaction();

        // drop tables
        $rslt = $this->db->query( "SELECT lower(table_name) FROM user_tables" );
        $rslt->setFetchMode( PDO::FETCH_NUM );
        $rows = $rslt->fetchAll();
        unset( $rslt );
        foreach ( $rows as $row )
        {
            $table = $row[0];
            $this->db->exec( "DROP TABLE $table" );
        }

        // drop sequences
        $rslt = $this->db->query( "SELECT LOWER(sequence_name) FROM user_sequences" );
        $rslt->setFetchMode( PDO::FETCH_NUM );
        $rows = $rslt->fetchAll();
        foreach ( $rows as $row )
        {
            $seq = $row[0];
            $this->db->exec( "DROP SEQUENCE $seq" );
        }

        // FIXME: drop triggers?

        $this->db->commit();
    }


    /**
     * Creates a new temporary table and returns the name.
     *
     * @throws ezcDbException::GENERIC_ERROR in case of inability to generate
     *         a unique temporary table name.
     * @see ezcDbHandler::createTemporaryTable()
     *
     *
     * @param   string $tableNamePattern  Name of temporary table user wants
     *                                    to create.
     * @param   string $tableDefinition Definition for the table, i.e.
     *                                  everything that goes between braces after
     *                                  CREATE TEMPORARY TABLE clause.
     * @return string                  Table name, that might have been changed
     *                                  by the handler to guarantee its uniqueness.
     * @todo move out
     */
    public function createTemporaryTable( $tableNamePattern, $tableDefinition )
    {
        if ( strpos( $tableNamePattern, '%' ) === false )
        {
            $tableName = $tableNamePattern;
        }
        else // generate unique table name with the given pattern
        {
            $maxTries = 10;
            do
            {
                $num = rand( 10000000, 99999999 );
                $tableName = strtoupper( str_replace( '%', $num, $tableNamePattern ) );
                $query = "SELECT count(*) AS cnt FROM user_tables WHERE table_name='$tableName'";
                $cnt = (int) $this->db->query( $query )->fetchColumn( 0 );
                $maxTries--;
            } while ( $cnt > 0 && $maxTries > 0 );

            if ( $maxTries == 0 )
            {
                throw ezcDbException(
                    ezcDbException::GENERIC_ERROR,
                    "Tried to generate an uninque temp table name for {$maxTries} time with no luck."
                );
            }
        }

        $this->db->exec( "CREATE GLOBAL TEMPORARY TABLE $tableName ($tableDefinition)" );
        return $tableName;
    }

    /**
     * Drop specified temporary table
     * in a portable way.
     *
     * Developers should use this method instead of dropping temporary
     * tables with the appropriate SQL queries
     * to maintain inter-DBMS portability.
     *
     * @see createTemporaryTable()
     *
     * @param   string  $tableName Name of temporary table to drop.
     * @return void
     */
    public function dropTemporaryTable( $tableName )
    {
        $this->db->exec( "TRUNCATE TABLE $tableName" );
        $this->db->exec( "DROP TABLE $tableName" );
    }
}
?>
