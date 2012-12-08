<?php
/**
 * File containing the ezcDbSchemaOracleWriter class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Handler for storing database schemas and applying differences that uses Oracle as backend.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaOracleWriter extends ezcDbSchemaCommonSqlWriter implements ezcDbSchemaDbWriter, ezcDbSchemaDiffDbWriter
{
    /**
     * Contains a type map from DbSchema types to Oracle native types.
     *
     * @var array
     */
    private $typeMap = array(
        'integer' => 'number',
        'boolean' => 'char',
        'float' => 'float',
        'decimal' => 'number',
        'date' => 'date',
        'timestamp' => 'timestamp',
        'text' => 'varchar2',
        'blob' => 'blob',
        'clob' => 'clob'
    );

    /**
     * Checks if query allowed.
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
        if ( strstr( $query, 'AUTO_INCREMENT' ) ) // detect AUTO_INCREMENT and return imediately. Will process later.
        {
            return false;
        }

        if ( substr( $query, 0, 10 ) == 'DROP TABLE' )
        {
            $tableName = substr($query, strlen( 'DROP TABLE "' ), -1 ); // get table name without quotes

            $result = $db->query( "SELECT count( table_name ) AS count FROM user_tables WHERE table_name='$tableName'" )->fetchAll();
            if ( $result[0]['count'] == 1 )
            {
                $sequences = $db->query( "SELECT sequence_name FROM user_sequences" )->fetchAll();
                array_walk( $sequences, create_function( '&$item,$key', '$item = $item[0];' ) );
                foreach ( $sequences as $sequenceName )
                {
                    // try to drop sequences related to dropped table.
                    if ( substr( $sequenceName, 0, strlen($tableName) ) == $tableName )
                    {
                        $db->query( "DROP SEQUENCE \"{$sequenceName}\"" );
                    }
                }
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
        foreach ( $this->convertDiffToDDL( $dbSchemaDiff, $db ) as $query )
        {
            if ( $this->isQueryAllowed( $db, $query ) )
            {
                $db->exec( $query );
            }
            else
            {
                if ( strstr($query, 'AUTO_INCREMENT') ) // detect AUTO_INCREMENT and emulate it by adding sequence and trigger
                {
                    $db->commit();
                    $db->beginTransaction();
                    if ( preg_match ( "/ALTER TABLE (.*) MODIFY (.*?) (.*) AUTO_INCREMENT/" , $query, $matches ) ) 
                    {
                        $tableName = trim( $matches[1], '"' );
                        $autoIncrementFieldName = trim( $matches[2], '"' );
                        $autoIncrementFieldType = trim( $matches[3], '"' );
                        $this->addAutoIncrementField( $db, $tableName, $autoIncrementFieldName, $autoIncrementFieldType );
                    }
                }
                $db->commit();
                $db->beginTransaction();
            }
        }
        $db->commit();
    }

    /**
    * Performs changing field in Oracle table.
    * (workaround for "ALTER TABLE table MODIFY field fieldType AUTO_INCREMENT " that not alowed in Oracle ).
    * 
    * @param ezcDbHandler    $db
    * @param string          $tableName
    * @param string          $autoIncrementFieldName
    * @param string          $autoIncrementFieldType
    */
    private function addAutoIncrementField( $db, $tableName, $autoIncrementFieldName, $autoIncrementFieldType )
    {
        // fetching field info from Oracle, getting column position of autoincrement field

        // @apichange This code piece would become orphan, with the new 
        // implementation. We still need it to drop the old sequences.
        // Remove until --END-- to not take care of them.
        $resultArray = $db->query( "SELECT   a.column_name AS field, " .    
                                   "         a.column_id AS field_pos " .
                                   "FROM     user_tab_columns a " .
                                   "WHERE    a.table_name = '{$tableName}' AND a.column_name = '{$autoIncrementFieldName}'" .
                                   "ORDER BY a.column_id" );
        $resultArray->setFetchMode( PDO::FETCH_ASSOC );

        if ( count( $resultArray) != 1 )
        {
            return;
        }

        $result = $resultArray->fetch();
        $fieldPos = $result['field_pos'];

        // emulation of autoincrement through adding sequence, trigger and constraint
        $oldName = ezcDbSchemaOracleHelper::generateSuffixCompositeIdentName( $tableName, $fieldPos, "seq" );
        $oldNameTrigger = ezcDbSchemaOracleHelper::generateSuffixCompositeIdentName( $tableName, $fieldPos, "trg" );
        $sequence = $db->query( "SELECT sequence_name FROM user_sequences WHERE sequence_name = '{$oldName}'" )->fetchAll();
        if ( count( $sequence) > 0  )
        {
            // assuming that if the seq exists, the trigger exists too
            $db->query( "DROP SEQUENCE \"{$oldName}\"" );
            $db->query( "DROP TRIGGER \"{$oldNameTrigger}\"" );
        }
        // --END--

        // New sequence names, using field names
        $newName = ezcDbSchemaOracleHelper::generateSuffixCompositeIdentName( $tableName, $fieldPos, "seq" );
        $newNameTrigger = ezcDbSchemaOracleHelper::generateSuffixCompositeIdentName( $tableName, $fieldPos, "seq" );
        // Emulation of autoincrement through adding sequence, trigger and constraint
        $sequences = $db->query( "SELECT sequence_name FROM user_sequences WHERE sequence_name = '{$newName}'" )->fetchAll();
        if ( count( $sequences ) > 0  )
        {
            $db->query( "DROP SEQUENCE \"{$newName}\"" );
        }

        $db->exec( "CREATE SEQUENCE \"{$newName}\" start with 1 increment by 1 nomaxvalue" );
        $db->exec( "CREATE OR REPLACE TRIGGER \"{$newNameTrigger}\" ".
                                  "before insert on \"{$tableName}\" for each row ".
                                  "begin ".
                                  "select \"{$newName}\".nextval into :new.\"{$autoIncrementFieldName}\" from dual; ".
                                  "end;" );

        $constraintName = ezcDbSchemaOracleHelper::generateSuffixedIdentName( array( $tableName ), "pkey" );
        $constraint = $db->query( "SELECT constraint_name FROM user_cons_columns WHERE constraint_name = '{$constraintName}'" )->fetchAll();
        if ( count( $constraint) > 0  )
        {
            $db->query( "ALTER TABLE \"$tableName\" DROP CONSTRAINT \"{$constraintName}\"" );
        }
        $db->exec( "ALTER TABLE \"{$tableName}\" ADD CONSTRAINT \"{$constraintName}\" PRIMARY KEY ( \"{$autoIncrementFieldName}\" )" );
        $this->context['skip_primary'] = true;
    }

    /**
     * Returns the differences definition in $dbSchema as database specific SQL DDL queries.
     *
     * @param ezcDbSchemaDiff $dbSchemaDiff
     * @param ezcDbHandler    $db
     *
     * @return array(string)
     */
    public function convertDiffToDDL( ezcDbSchemaDiff $dbSchemaDiff, ezcDbHandler $db = null )
    {
        $this->diffSchema = $dbSchemaDiff;

        // reset queries
        $this->queries = array();
        $this->context = array();

        // Find sequences which require explicit drop statesments, see bug 
        // #16222
        if ( $db !== null )
        {
            $this->generateAdditionalDropSequenceStatements( $dbSchemaDiff, $db );
        }

        $this->generateDiffSchemaAsSql();
        return $this->queries;
    }

    /**
     * Generate additional drop sequence statements
     *
     * Some sequences might not be dropped automatically, this method generates 
     * additional DROP SEQUENCE queries for those.
     *
     * Since Oracle only allows sequence identifiers up to 30 characters 
     * sequences for long table / column names may be shortened. In this case 
     * the sequence name does not started with the table name any more, thus 
     * does not get dropped together with the table automatically.
     *
     * This method requires a DB connection to check which sequences have been 
     * defined in the database, because the information about fields is not 
     * available otherwise.
     * 
     * @param ezcDbSchemaDiff $dbSchemaDiff 
     * @param ezcDbHandler $db 
     * @return void
     */
    protected function generateAdditionalDropSequenceStatements( ezcDbSchemaDiff $dbSchemaDiff, ezcDbHandler $db )
    {
        $reader = new ezcDbSchemaOracleReader();
        $schema = $reader->loadFromDb( $db )->getSchema();
        foreach ( $dbSchemaDiff->removedTables as $table => $true )
        {
            foreach ( $schema[$table]->fields as $name => $field )
            {
                if ( $field->autoIncrement !== true )
                {
                    continue;
                }

                $seqName = ezcDbSchemaOracleHelper::generateSuffixCompositeIdentName( $table, $name, "seq" );
                if ( strpos( $seqName, $table ) !== 0 )
                {
                    $this->queries[] = "DROP SEQUENCE \"$seqName\"";
                }
            }
        }
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
            else
            {
                $typeAddition = "(4000)"; // default length for varchar2 in Oracle
            }
        }
        if ( $fieldDefinition->type == 'boolean' )
        {
            $typeAddition = "(1)";
            if ( $fieldDefinition->default )
            {
                $fieldDefinition->default = ( $fieldDefinition->default == 'true' ) ? '1': '0';
            }
        }

        if ( !isset( $this->typeMap[$fieldDefinition->type] ) )
        {
            throw new ezcDbSchemaUnsupportedTypeException( 'Oracle', $fieldDefinition->type );
        }
        $type = $this->typeMap[$fieldDefinition->type];

        return "$type$typeAddition";
    }

    /**
     * Adds a "create table" query for the table $tableName with 
     * definition $tableDefinition to the internal list of queries.
     * 
     * Adds additional CREATE queries for sequences and triggers
     * to implement autoincrement fields that not supported in Oracle directly.
     * 
     * @param string           $tableName
     * @param ezcDbSchemaTable $tableDefinition
     */
    protected function generateCreateTableSql( $tableName, ezcDbSchemaTable $tableDefinition )
    {
        $sql = '';
        $sql .= "CREATE TABLE \"{$tableName}\" (\n";
        $this->context['skip_primary'] = false;

        // dump fields
        $fieldsSQL = array();
        $autoincrementSQL = array();
        $fieldCounter = 1;

        foreach ( $tableDefinition->fields as $fieldName => $fieldDefinition )
        {
            $fieldsSQL[] = "\t" . $this->generateFieldSql( $fieldName, $fieldDefinition );

            if ( $fieldDefinition->autoIncrement && !$this->context['skip_primary'] )
            {
                $sequenceName = ezcDbSchemaOracleHelper::generateSuffixCompositeIdentName( $tableName, $fieldName, "seq" );
                $triggerName = ezcDbSchemaOracleHelper::generateSuffixCompositeIdentName( $tableName, $fieldName, "trg" );
                $constraintName = ezcDbSchemaOracleHelper::generateSuffixedIdentName( array( $tableName ), "pkey" );
                $autoincrementSQL[] = "CREATE SEQUENCE \"{$sequenceName}\" start with 1 increment by 1 nomaxvalue";
                $autoincrementSQL[] = "CREATE OR REPLACE TRIGGER \"{$triggerName}\" ".
                                          "before insert on \"{$tableName}\" for each row ".
                                          "begin ".
                                          "select \"{$sequenceName}\".nextval into :new.\"{$fieldName}\" from dual; ".
                                          "end;";
                $autoincrementSQL[] = "ALTER TABLE \"{$tableName}\" ADD CONSTRAINT \"{$constraintName}\" PRIMARY KEY ( \"{$fieldName}\" )";
                $this->context['skip_primary'] = true;
            }
            $fieldCounter++;
        }

        $sql .= join( ",\n", $fieldsSQL );
        $sql .= "\n)";

        $this->queries[] = $sql;

        if ( count( $autoincrementSQL ) > 0 ) // adding autoincrement emulation queries if exists
        {
            $this->queries = array_merge( $this->queries, $autoincrementSQL );
        }

        // dump indexes
        foreach ( $tableDefinition->indexes as $indexName => $indexDefinition)
        {
            $fieldsSQL[] = $this->generateAddIndexSql( $tableName, $indexName, $indexDefinition );
        }
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
        if ( !$fieldDefinition->autoIncrement )
        {
            $this->queries[] = "ALTER TABLE \"$tableName\" MODIFY " .
                               $this->generateFieldSql( $fieldName, $fieldDefinition );
        }
        else
        {    // mark query to make autoincrement emulation when executing
            $this->queries[] = "ALTER TABLE \"$tableName\" MODIFY " .
                               $this->generateFieldSql( $fieldName, $fieldDefinition ) .
                               " AUTO_INCREMENT";
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
        $this->queries[] = "ALTER TABLE \"$tableName\" DROP COLUMN \"$fieldName\"";
    }

    /**
     * Returns a column definition for $fieldName with definition $fieldDefinition.
     *
     * @param  string           $fieldName
     * @param  ezcDbSchemaField $fieldDefinition
     * @param  string           $autoincrementField
     * @return string
     */
    protected function generateFieldSql( $fieldName, ezcDbSchemaField $fieldDefinition )
    {
        $sqlDefinition = '"'.$fieldName.'" ';
        $defList = array();

        $type = $this->convertFromGenericType( $fieldDefinition );
        $defList[] = $type;

        if ( !is_null( $fieldDefinition->default ) && !$fieldDefinition->autoIncrement )
        {
            $default = $this->generateDefault( $fieldDefinition->type, $fieldDefinition->default );
            $defList[] = "DEFAULT $default";
        }

        if ( $fieldDefinition->notNull )
        {
            $defList[] = 'NOT NULL';
        }

        $sqlDefinition .= join( ' ', $defList );

        return $sqlDefinition;
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
                return ( $value && $value != 'false' ) ? '1' : '0';

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
     * Adds a "alter table" query to remote the index $indexName from the table $tableName to the internal list of queries.
     *
     * @param string           $tableName
     * @param string           $indexName
     */
    protected function generateDropIndexSql( $tableName, $indexName )
    {
        if ( $indexName == 'primary' ) // handling primary indexes
        {
            $constraintName = ezcDbSchemaOracleHelper::generateSuffixedIdentName( array( $tableName ), "pkey");
            $this->queries[] = "ALTER TABLE \"$tableName\" DROP CONSTRAINT \"{$constraintName}\"";
        }
        else
        {
            $this->queries[] = "DROP INDEX \"$indexName\"";
        }
    }
}
?>
