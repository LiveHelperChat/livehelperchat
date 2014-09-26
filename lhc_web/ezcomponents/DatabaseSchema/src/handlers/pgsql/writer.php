<?php
/**
 * File containing the ezcDbSchemaPgsqlWriter class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Handler for storing database schemas and applying differences that uses PostgreSQL as backend.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaPgsqlWriter extends ezcDbSchemaCommonSqlWriter implements ezcDbSchemaDbWriter, ezcDbSchemaDiffDbWriter
{
    /**
     * Contains a type map from DbSchema types to PostgreSQL native types.
     *
     * @var array
     */
    private $typeMap = array(
        'integer' => 'bigint',
        'boolean' => 'boolean',
        'float' => 'double precision',
        'decimal' => 'numeric',
        'date' => 'date',
        'timestamp' => 'timestamp',
        'text' => 'varchar',
        'blob' => 'bytea',
        'clob' => 'text'
    );

    /**
     * Creates tables defined in $dbSchema in the database referenced by $db.
     *
     * If table already exists it will be removed first.
     * This method uses {@link convertToDDL} to create SQL for the schema
     * definition and then executes the return SQL statements on the database
     * handler $db.
     *
     * @todo check for failed transaction
     *
     * @param ezcDbHandler $db
     * @param ezcDbSchema  $dbSchema
     */
    public function saveToDb( ezcDbHandler $db, ezcDbSchema $dbSchema )
    {
        $db->beginTransaction();
        foreach ( $this->convertToDDL( $dbSchema ) as $query )
        {
            if ( $this->isQueryAllowed( $db, $query ) ) 
            {
                $db->exec( $query );
            }
            else  
            {
                // workarounds for SQL syntax
                // "ALTER TABLE tab ALTER col TYPE type"
                // and "ALTER TABLE tab ADD col type NOT NULL"
                // that works in PostgreSQL 8.x but not 
                // supported in PostgreSQL 7.x

                if ( preg_match( "/ALTER TABLE (.*) ALTER (.*) TYPE (.*) USING CAST\((.*)\)/" , $query, $matches ) ) 
                {
                    $tableName = $matches[1];
                    $fieldName = $matches[2];
                    $fieldType = $matches[3];
                    $this->changeField( $db, $tableName, $fieldName, $fieldType );
                }
                else if ( preg_match( "/ALTER TABLE (.*) ADD (.*) (.*) NOT NULL/" , $query, $matches ) ) 
                {
                    $tableName = $matches[1];
                    $fieldName = $matches[2];
                    $fieldType = $matches[3];
                    $this->addField( $db, $tableName, $fieldName, $fieldType );
                }
            }
        }
        $db->commit();
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
     *
     * @return boolean false if query should not be executed.
     */
    public function isQueryAllowed( ezcDbHandler $db, $query )
    {
        if ( substr( $query, 0, 10 ) == 'DROP TABLE' )
        {
            $tableName = trim( substr( $query, strlen( 'DROP TABLE ' ) ), '"' );
            $result = $db->query( "SELECT count(*) FROM pg_tables WHERE tablename='$tableName'" )->fetchAll();
            if ( $result[0]['count'] == 1 )
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        if ( preg_match( "/ALTER TABLE (.*) ALTER (.*) TYPE (.*)/" , $query, $matches ) ||
             preg_match( "/ALTER TABLE (.*) ADD (.*) NOT NULL/" , $query, $matches )
           )
        {
            return false;
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
                // workarounds for SQL syntax
                // "ALTER TABLE tab ALTER col TYPE type"
                // and "ALTER TABLE tab ADD col type NOT NULL"
                // that works in PostgreSQL 8.x but not 
                // supported in PostgreSQL 7.x

                if ( preg_match( "/ALTER TABLE (.*) ALTER (.*) TYPE (.*) USING CAST\((.*)\)/" , $query, $matches ) ) 
                {
                    $tableName = trim( $matches[1], '"' );
                    $fieldName = trim( $matches[2], '"' );
                    $fieldType = trim( $matches[3], '"' );
                    $this->changeField( $db, $tableName, $fieldName, $fieldType );
                }
                else if ( preg_match( "/ALTER TABLE (.*) ADD (.*) (.*) NOT NULL/" , $query, $matches ) ) 
                {
                    $tableName = trim( $matches[1], '"' );
                    $fieldName = trim( $matches[2], '"' );
                    $fieldType = trim( $matches[3], '"' );
                    $this->addField( $db, $tableName, $fieldName, $fieldType );
                }
            }
        }
        $db->commit();
    }

    /**
     * Returns a "CREATE TABLE" SQL statement part for the table $tableName.
     *
     * @param string  $tableName
     * @return string
     */
    protected function generateCreateTableSqlStatement( $tableName )
    {
        return "CREATE TABLE \"{$tableName}\"";
    }

    /**
     * Performs changing field in PostgreSQL table.
     * ( workaround for "ALTER TABLE table ALTER field TYPE fieldDefinition" 
     * that not alowed in PostgreSQL 7.x but works in PostgreSQL 8.x ).
     * 
     * @param ezcDbHandler    $db
     * @param string          $tableName
     * @param string          $changeFieldName
     * @param string          $changeFieldType
     *
     */
    private function changeField( ezcDbHandler $db, $tableName, $changeFieldName, $changeFieldType )
    {
        $db->exec( "ALTER TABLE \"{$tableName}\" RENAME COLUMN \"{$changeFieldName}\" TO \"{$changeFieldName}_old\";" );
        $db->exec( "ALTER TABLE \"{$tableName}\" ADD COLUMN \"{$changeFieldName}\" {$changeFieldType};" );
        $db->exec( "UPDATE \"{$tableName}\" SET  \"{$changeFieldName}\" = \"{$changeFieldName}_old\";" );
        $db->exec( "ALTER TABLE \"{$tableName}\" DROP COLUMN \"{$changeFieldName}_old\";" );
    }

    /**
     * Performs adding field in PostgreSQL table.
     * ( workaround for "ALTER TABLE table ADD field fieldDefinition NOT NULL" 
     * that not alowed in PostgreSQL 7.x but works in PostgreSQL 8.x ).
     * 
     * @param ezcDbHandler    $db
     * @param string          $tableName
     * @param string          $fieldName
     * @param string          $fieldType
     *
     */
    private function addField( ezcDbHandler $db, $tableName, $fieldName, $fieldType )
    {
        $db->exec( "ALTER TABLE \"{$tableName}\" ADD \"{$fieldName}\" {$fieldType}" );
        $db->exec( "ALTER TABLE \"{$tableName}\" ALTER \"{$fieldName}\" SET NOT NULL" );
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
     * @param string $tableName
     */
    protected function generateDropTableSql( $tableName )
    {
        $this->queries[] = "DROP TABLE \"$tableName\"";
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

        if ( !isset( $this->typeMap[$fieldDefinition->type] ) )
        {
            throw new ezcDbSchemaUnsupportedTypeException( 'PostGreSQL', $fieldDefinition->type );
        }
        $type = $this->typeMap[$fieldDefinition->type];

        return "$type$typeAddition";
    }

    /**
     * Adds a "create table" query for the table $tableName with 
     * definition $tableDefinition to the internal list of queries.
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
        $this->queries[] = "ALTER TABLE \"$tableName\" ADD " . $this->generateFieldSql( $fieldName, $fieldDefinition );
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
        $fieldType = strstr(  $this->generateFieldSql( $fieldName, $fieldDefinition ), ' ' );
        if ( $fieldDefinition->autoIncrement ) 
        {
            $this->queries[] = "CREATE SEQUENCE \"{$tableName}_{$fieldName}_seq\"";
            $this->queries[] = "ALTER TABLE \"$tableName\" ALTER \"$fieldName\" TYPE INTEGER";
            $this->queries[] = "ALTER TABLE \"$tableName\" ALTER COLUMN \"$fieldName\" SET DEFAULT nextval('{$tableName}_{$fieldName}_seq')";
            $this->queries[] = "ALTER TABLE \"$tableName\" ALTER COLUMN \"$fieldName\" SET NOT NULL";
        }
        else
        {
            $this->queries[] = "ALTER TABLE \"$tableName\" ALTER \"$fieldName\" TYPE".$fieldType." USING CAST(\"$fieldName\" AS $fieldType)";
        }
    }

    /**
     * Adds a "alter table" query to drop the field $fieldName from $tableName.
     *
     * @param string $tableName
     * @param string $fieldName
     */
    protected function generateDropFieldSql( $tableName, $fieldName )
    {
        $this->queries[] = "ALTER TABLE \"$tableName\" DROP \"$fieldName\"";
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
        $sqlDefinition = "\"$fieldName\" ";

        $defList = array();

        if ( $fieldDefinition->autoIncrement )
        {
            $type = 'serial';
            $defList[] = $type;

            if ( $this->context['skip_primary'] == false ) 
            {
                $this->context['skip_primary'] = true;
                $defList[] = 'PRIMARY KEY';
            }
        }
        else
        {
            $type = $this->convertFromGenericType( $fieldDefinition );
            $defList[] = $type;
        }

        if ( $fieldDefinition->notNull )
        {
            $defList[] = 'NOT NULL';
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
     * Adds a "alter table" query to add the index $indexName to the table $tableName with definition $indexDefinition to the internal list of queries
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
            $sql = "ALTER TABLE \"$tableName\" ADD CONSTRAINT \"{$tableName}_pkey\" PRIMARY KEY";
        }
        else if ( $indexDefinition->unique )
        {
            $sql = "CREATE UNIQUE INDEX \"$indexName\" ON \"$tableName\"";
        }
        else
        {
            $sql = "CREATE INDEX \"$indexName\" ON \"$tableName\"";
        }

        $sql .= " ( ";

        $indexFieldSql = array();
        foreach ( $indexDefinition->indexFields as $indexFieldName => $dummy )
        {
                $indexFieldSql[] = "\"$indexFieldName\"";
        }
        $sql .= join( ', ', $indexFieldSql ) . " )";

        $this->queries[] = $sql;
    }
    
    /**
     * Adds a "alter table" query to remove the index $indexName from the table $tableName to the internal list of queries.
     *
     * @param string           $tableName
     * @param string           $indexName
     */
    protected function generateDropIndexSql( $tableName, $indexName )
    {
        if ( $indexName == 'primary' ) // handling primary indexes
        {
            $this->queries[] = "ALTER TABLE \"$tableName\" DROP CONSTRAINT {$tableName}_pkey";
        }
        else
        {
            $this->queries[] = "DROP INDEX \"$indexName\"";
        }
    }
}
?>
