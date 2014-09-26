<?php
/**
 * File containing the ezcDbSchemaSqliteWriter class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Handler for storing database schemas and applying differences that uses SQLite as backend.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaSqliteWriter extends ezcDbSchemaCommonSqlWriter
{
    /**
     * Contains a type map from DbSchema types to SQLite native types.
     *
     * @var array
     */
    private $typeMap = array(
        'integer' => 'integer',
        'boolean' => 'integer',
        'float' => 'real',
        'decimal' => 'numeric',
        'date' => 'date',
        'timestamp' => 'timestamp',
        'text' => 'text',
        'blob' => 'blob',
        'clob' => 'clob'
    );

    /**
     * Returns what type of schema writer this class implements.
     *
     * This method always returns ezcDbSchema::DATABASE
     *
     * @return int
     */
    public function getWriterType()
    {
        return ezcDbSchema::DATABASE;
    }

    /**
     * Checks if certain query allowed.
     *
     * Perform testing if table exist for DROP TABLE query 
     * to avoid stoping execution while try to drop not existent table.
     * 
     * @param ezcDbHandler $db
     * @param string       $query
     *
     * @return boolean false if query should not be executed.
     */
    public function isQueryAllowed( ezcDbHandler $db, $query )
    {
        if ( strstr($query, 'DROP COLUMN') || strstr($query, 'CHANGE') ) // detecting DROP COLUMN clause or field CHANGE clause 
        {
            return false;
        }

        if ( substr( $query, 0, 10 ) == 'DROP TABLE' )
        {
            $tableName = substr( $query, strlen( 'DROP TABLE ' ) );
            $result = $db->query( "SELECT count(*) AS count FROM 
                                    (SELECT * FROM sqlite_master UNION ALL 
                                     SELECT * FROM sqlite_temp_master)
                                   WHERE type='table' AND tbl_name={$tableName}" )->fetchAll();
            if ( $result[0]['count'] == 1 )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns what type of schema difference writer this class implements.
     *
     * This method always returns ezcDbSchema::DATABASE
     *
     * @return int
     */
    public function getDiffWriterType()
    {
        return ezcDbSchema::DATABASE;
    }
    /**
     * Applies the differences defined in $dbSchemaDiff to the database referenced by $db.
     *
     * This method uses {@link convertDiffToDDL} to create SQL for the
     * differences and then executes the returned SQL statements on the
     * database handler $db.
     *
     * @todo check for failed transaction
     *
     * @param ezcDbHandler    $db
     * @param ezcDbSchemaDiff $dbSchemaDiff
     */
    public function applyDiffToDb( ezcDbHandler $db, ezcDbSchemaDiff $dbSchemaDiff )
    {
        $db->beginTransaction();
        foreach ( $this->convertDiffToDDL( $dbSchemaDiff ) as $query )
        {
            if ( $this->isQueryAllowed( $db, $query ) ) 
            {
                $db->exec( $query );
            }
            else
            {
                // SQLite don't support SQL clause for removing columns
                // perform emulation for this
                if ( strstr( $query, 'DROP COLUMN' ) ) 
                {
                    $db->commit();
                    $db->beginTransaction();
                    try
                    {
                        preg_match ( "/ALTER TABLE (.*) DROP COLUMN (.*)/" , $query, $matches );
                        if ( !$matches ) 
                        {
                            throw new ezcDbSchemaSqliteDropFieldException( 
                                            "Can't fetch field for droping from SQL query: $query" );
                        }

                        $tableName = trim( $matches[1], "'" );
                        $dropFieldName = trim( $matches[2], "'" );

                        $this->dropField( $db, $tableName , $dropFieldName );
                    }
                    catch ( ezcDbSchemaSqliteDropFieldException $e )
                    {
                    }
                    $db->commit();
                    $db->beginTransaction();
                }
                else if ( strstr( $query, 'CHANGE' ) ) // SQLite don't support SQL clause for changing columns 
                                                       // perform emulation for this

                {
                    $db->commit();
                    $db->beginTransaction();
                    try
                    {
                        preg_match( "/ALTER TABLE (.*) CHANGE (.*?) (.*?) (.*)/" , $query, $matches );
                        $tableName = trim( $matches[1], "'" );
                        $changeFieldName = trim( $matches[2], "'" );
                        $changeFieldNewName = trim( $matches[3], "'" );
                        $changeFieldNewType = $matches[4];
                        $this->changeField( $db, $tableName, $changeFieldName, $changeFieldNewName, $changeFieldNewType );

                    }
                    catch ( ezcDbSchemaSqliteDropFieldException $e )
                    {
                    }
                    $db->commit();
                    $db->beginTransaction();
                }

            }
        }
        $db->commit();
    }

    /**
     * Performs changing field in SQLite table.
     * (workaround for "ALTER TABLE table CHANGE field fieldDefinition" that not alowed in SQLite ).
     * 
     * @param ezcDbHandler    $db
     * @param string          $tableName
     * @param string          $changeFieldName
     * @param string          $changeFieldNewName
     * @param string          $changeFieldNewDefinition
     */
    private function changeField( ezcDbHandler $db, $tableName, $changeFieldName, $changeFieldNewName, $changeFieldNewDefinition )
    {
        $tmpTableName = $tableName.'_ezcbackup';

        $resultArray = $db->query( "PRAGMA TABLE_INFO( '$tableName' )" );
        $resultArray->setFetchMode( PDO::FETCH_NUM );

        $fieldsDefinitions = array();
        $fieldsList = array();

        foreach ( $resultArray as $row )
        {
            $fieldSql = array();
            $fieldSql[] = $row[1]; // name
            if ( $row[1] == $changeFieldName )
            {
                // will recreate changed field with new definition
                $fieldsDefinitions[] = "'$changeFieldNewName' $changeFieldNewDefinition";
                $fieldsList[] = $fieldSql[0];
                continue; 
            }

            $fieldSql[] = $row[2]; // type

            if ( $row[3] == '99' )
            {
                $fieldSql[] = 'NOT NULL';
            }

            $fieldDefault = null;
            if ( $row[4] != '' )
            {
                $fieldSql[]= "DEFAULT '{$row[4]}'";
            }

            if ( $row[5] =='1' )
            {
                $fieldSql[] = 'PRIMARY KEY AUTOINCREMENT';
            }

            // FIXME: unsigned needs to be implemented
            $fieldUnsigned = false;

            $fieldsDefinitions[] = join ( ' ', $fieldSql );
            $fieldsList[] = $fieldSql[0];
        }

        if ( count( $fieldsDefinitions ) > 0 )
        {
            $fields = join( ', ', $fieldsDefinitions );
            $tmpTableCreateSql = "CREATE TEMPORARY TABLE '$tmpTableName'( $fields  );";
            $newTableCreateSql = "CREATE TABLE '$tableName'( $fields )" ;
            if ( count($fieldsList)>0 ) 
            {
                $db->exec( $tmpTableCreateSql );
                $db->exec( "INSERT INTO '$tmpTableName' SELECT ". join( ', ', $fieldsList )." FROM '$tableName';" );
                $db->exec( "DROP TABLE '$tableName';" );
                $db->exec( $newTableCreateSql );
                $db->exec( "INSERT INTO '$tableName' SELECT ". join( ', ', $fieldsList )." FROM '$tmpTableName';" );
                $db->exec( "DROP TABLE '$tmpTableName';" );
            }
            else
            {
                // we had table with one column will drop table and recreate with changed column.
                $db->exec( "DROP TABLE '$tableName';" );
                $newTableCreateSql = "CREATE TABLE '$tableName'( $changeFieldNewName $changeFieldNewDefinition )" ;
                $db->exec( $newTableCreateSql );
            }
        }
    }

    /**
     * Performs droping field from SQLite table using temporary table
     * (workaround for "ALTER TABLE table DROP field" that not alowed in SQLite ).
     *
     * @param ezcDbHandler    $db
     * @param string          $tableName
     * @param string          $dropFieldName
     */
    private function dropField( $db,  $tableName , $dropFieldName )
    {
        $tmpTableName = $tableName.'_ezcbackup';

        $resultArray = $db->query( "PRAGMA TABLE_INFO( $tableName )" );
        $resultArray->setFetchMode( PDO::FETCH_NUM );

        $fieldsDefinitions = array();
        $fieldsList = array();

        foreach ( $resultArray as $row )
        {
            $fieldSql = array();
            $fieldSql[] = "'{$row[1]}'"; // name
            if ( $row[1] == $dropFieldName )
            {
                continue; // don't include droped fileld in temporary table
            }

            $fieldSql[] = $row[2]; // type

            if ( $row[3] == '99' )
            {
                $fieldSql[] = 'NOT NULL';
            }

            $fieldDefault = null;
            if ( $row[4] != '' )
            {
                $fieldSql[]= "DEFAULT '{$row[4]}'";
            }

            if ( $row[5] =='1' )
            {
                $fieldSql[] = 'PRIMARY KEY AUTOINCREMENT';
            }

            // FIXME: unsigned needs to be implemented
            $fieldUnsigned = false;

            $fieldsDefinitions[] = join ( ' ', $fieldSql );
            $fieldsList[] = $fieldSql[0];
        }

        $fields = join( ', ', $fieldsDefinitions );
        $tmpTableCreateSql = "CREATE TEMPORARY TABLE '$tmpTableName'( $fields  );";
        $newTableCreateSql = "CREATE TABLE '$tableName'( $fields )" ;
        if ( count( $fieldsList ) > 0 ) 
        {
            $db->exec( $tmpTableCreateSql );
            $db->exec( "INSERT INTO '$tmpTableName' SELECT ". join( ', ', $fieldsList )." FROM '$tableName';" );
            $db->exec( "DROP TABLE '$tableName';" );
            $db->exec( $newTableCreateSql );
            $db->exec( "INSERT INTO '$tableName' SELECT ". join( ', ', $fieldsList )." FROM '$tmpTableName';" );
            $db->exec( "DROP TABLE '$tmpTableName';" );
        }
        else
        {
            throw new ezcDbSchemaDropAllColumnsException( 
                            "Trying to delete all columns in table: $tableName" );
        }
    }


    /**
     * Returns the differences definition in $dbSchema as database specific SQL DDL queries.
     *
     * @param ezcDbSchemaDiff $dbSchemaDiff
     *
     * @return array(string)
     */
    public function convertDiffToDDL( ezcDbSchemaDiff $dbSchemaDiff )
    {
        $this->diffSchema = $dbSchemaDiff;

        // reset queries
        $this->queries = array();
        $this->context = array();

        $this->generateDiffSchemaAsSql();
        return $this->queries;
    }

    /**
     * Adds a "drop table" query for the table $tableName to the internal list of queries.
     * 
     * @todo use DROP IF EXISTS that supported since SQLite 3.3
     *
     * @param string $tableName
     */
    protected function generateDropTableSql( $tableName )
    {
        // use DROP TABLE and isQueryAllowed() workaround to emulate DROP TABLE IF EXISTS.
        $this->queries[] = "DROP TABLE '$tableName'";
    }

    /**
     * Returns an appropriate default value for $type with $value.
     *
     * @param string $type
     * @param mixed  $value
     * @return string
     */
    protected function generateDefault( $type, $value )
    {
        switch ( $type )
        {
            case 'boolean':
                return ( $value && $value !== 'false' ) ? '1' : '0';

            case 'integer':
                return (int) $value;

            case 'float':
            case 'decimal':
                return (float) $value;

            default:
                return "'$value'";
        }
    }

    /**
     * Converts the generic field type contained in $fieldDefinition to a database specific field definition.
     *
     * @param ezcDbSchemaField $fieldDefinition
     * @return string
     */
    protected function convertFromGenericType( ezcDbSchemaField $fieldDefinition )
    {
        $typeAddition = '';
        if ( in_array( $fieldDefinition->type, array( 'decimal', 'text' ) ) )
        {
            if ( $fieldDefinition->length !== false && $fieldDefinition->length !== 0 )
            {
                $typeAddition = "({$fieldDefinition->length})";
            }
        }
        if ( $fieldDefinition->type == 'text' && !$fieldDefinition->length )
        {
            $typeAddition = "(255)";
        }
        if ( $fieldDefinition->type == 'boolean' )
        {
            $typeAddition = '(1)';
        }

        if ( !isset( $this->typeMap[$fieldDefinition->type] ) )
        {
            throw new ezcDbSchemaUnsupportedTypeException( 'SQLite', $fieldDefinition->type );
        }
        $type = $this->typeMap[$fieldDefinition->type];

        return "$type$typeAddition";
    }

    /**
     * Returns a "CREATE TABLE" SQL statement part for the table $tableName.
     *
     * @param string  $tableName
     * @return string
     */
    protected function generateCreateTableSqlStatement( $tableName )
    {
        return "CREATE TABLE '{$tableName}'";
    }

    /**
     * Adds a "create table" query for the table $tableName with definition $tableDefinition to the internal list of queries.
     *
     * @param string           $tableName
     * @param ezcDbSchemaTable $tableDefinition
     */
    protected function generateCreateTableSql( $tableName, ezcDbSchemaTable $tableDefinition )
    {
        $this->context['skip_primary'] = false;
        parent::generateCreateTableSql( $tableName, $tableDefinition );
    }

    /**
     * Generates queries to upgrade a the table $tableName with the differences in $tableDiff.
     *
     * This method generates queries to migrate a table to a new version
     * with the changes that are stored in the $tableDiff property. It
     * will call different subfunctions for the different types of changes, and
     * those functions will add queries to the internal list of queries that is
     * stored in $this->queries.
     *
     * @param string $tableName
     * @param ezcDbSchemaTableDiff $tableDiff
     */
    protected function generateDiffSchemaTableAsSql( $tableName, ezcDbSchemaTableDiff $tableDiff )
    {
        $this->context['skip_primary'] = false;
        parent::generateDiffSchemaTableAsSql( $tableName, $tableDiff );
    }

    /**
     * Adds a "alter table" query to add the field $fieldName to $tableName with the definition $fieldDefinition.
     *
     * @param string           $tableName
     * @param string           $fieldName
     * @param ezcDbSchemaField $fieldDefinition
     */
    protected function generateAddFieldSql( $tableName, $fieldName, ezcDbSchemaField $fieldDefinition )
    {
        if ( $fieldDefinition->notNull && $fieldDefinition->default == null ) 
        {
            $fieldDefinition->default = $this->generateDefault( $fieldDefinition->type, 0 );

        }
        $this->queries[] = "ALTER TABLE '$tableName' ADD " . $this->generateFieldSql( $fieldName, $fieldDefinition );
    }

    /**
     * Adds a "alter table" query to change the field $fieldName to $tableName with the definition $fieldDefinition.
     *
     * @param string           $tableName
     * @param string           $fieldName
     * @param ezcDbSchemaField $fieldDefinition
     */
    protected function generateChangeFieldSql( $tableName, $fieldName, ezcDbSchemaField $fieldDefinition )
    {
        $this->queries[] = "ALTER TABLE '$tableName' CHANGE '$fieldName' " . $this->generateFieldSql( $fieldName, $fieldDefinition );
    }

    /**
     * Adds a "alter table" query to drop the field $fieldName from $tableName.
     * will be hooked on execution stage and workaround using temporary 
     * table will be performed.
     *
     * @param string $tableName
     * @param string $fieldName
     */
    protected function generateDropFieldSql( $tableName, $fieldName )
    {
        $this->queries[] = "ALTER TABLE '$tableName' DROP COLUMN '$fieldName'";
    }

    /**
     * Returns a column definition for $fieldName with definition $fieldDefinition.
     *
     * @param  string           $fieldName
     * @param  ezcDbSchemaField $fieldDefinition
     * @return string
     */
    protected function generateFieldSql( $fieldName, ezcDbSchemaField $fieldDefinition )
    {
        $sqlDefinition = "'$fieldName' ";
        $defList = array();

        $type = $this->convertFromGenericType( $fieldDefinition );
        $defList[] = $type;

        if ( $fieldDefinition->notNull )
        {
            $defList[] = 'NOT NULL';
        }

        if ( $fieldDefinition->autoIncrement )
        {
            $defList[] = "PRIMARY KEY AUTOINCREMENT";
            $this->context['skip_primary'] = true;
        }
        
        if ( !is_null( $fieldDefinition->default ) && !$fieldDefinition->autoIncrement )
        {
            $default = $this->generateDefault( $fieldDefinition->type, $fieldDefinition->default );
            $defList[] = "DEFAULT $default";
        }

        $sqlDefinition .= join( ' ', $defList );

        return $sqlDefinition;
    }

    /**
     * Adds a "create index" query to add the index $indexName to the 
     * table $tableName with definition $indexDefinition to the internal list of queries
     *
     * @param string           $tableName
     * @param string           $indexName
     * @param ezcDbSchemaIndex $indexDefinition
     */
    protected function generateAddIndexSql( $tableName, $indexName, ezcDbSchemaIndex $indexDefinition )
    {
        $sql = "";
        if ( $indexDefinition->primary )
        {
            if ( $this->context['skip_primary'] )
            {
                return;
            }
            if ( $indexName == 'primary' ) 
            {
                $indexName = $tableName.'_pri';
            }
            $sql = "CREATE UNIQUE INDEX '$indexName' ON '$tableName'";
        }
        else if ( $indexDefinition->unique )
        {
            $sql = "CREATE UNIQUE INDEX '$indexName' ON '$tableName'";
        }
        else
        {
            $sql = "CREATE INDEX '$indexName' ON '$tableName'";
        }

        $sql .= " ( ";

        $indexFieldSql = array();
        foreach ( $indexDefinition->indexFields as $indexFieldName => $dummy )
        {
            $indexFieldSql[] = "'$indexFieldName'";
        }
        $sql .= join( ', ', $indexFieldSql ) . " )";

        $this->queries[] = $sql;
    }
    
    /**
     * Adds a "alter table" query to revote the index $indexName from the table $tableName to the internal list of queries.
     *
     * @param string           $tableName
     * @param string           $indexName
     */
    protected function generateDropIndexSql( $tableName, $indexName )
    {
        if ( $indexName == 'primary') 
        {
            $indexName = $tableName.'_pri';
        }
        $this->queries[] = "DROP INDEX '$indexName'";
    }
}
?>
