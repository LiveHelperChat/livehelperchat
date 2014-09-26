<?php
/**
 * File containing the ezcDbSchemaSqliteReader class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Handler for SQLite connections representing a DB schema.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 */
class ezcDbSchemaSqliteReader extends ezcDbSchemaCommonSqlReader implements ezcDbSchemaDbReader
{
    /**
     * Contains a type map from SQLite native types to generic DbSchema types.
     *
     * @var array
     */
    static private $typeMap = array(
        'integer' => 'integer',
        'integer unsigned' => 'integer',
        'real' => 'float',
        'float' => 'float',
        'text' => 'text',
        'varchar' => 'text',
        'blob' => 'blob',
        'clob' => 'clob',
        'boolean' => 'boolean',
        'numeric' => 'decimal',
        'date' => 'date',
        'datetime' => 'timestamp',
        'timestamp' => 'timestamp'
    );

    /**
     * Loops over all the tables in the database and extracts schema information.
     *
     * This method extracts information about a database's schema from the
     * database itself and returns this schema as an ezcDbSchema object.
     *
     * @return ezcDbSchema
     */
    protected function fetchSchema()
    {
        $tables = $this->db->query( "SELECT name FROM sqlite_master WHERE type='table' AND name != 'sqlite_sequence' ORDER BY name" )->fetchAll();
        return $this->processSchema( $tables );
    }

    /**
     * Fetch fields definition for the table $tableName
     *
     * This method loops over all the fields in the table $tableName and
     * returns an array with the field specification. The key in the returned
     * array is the name of the field.
     *
     * @param string $tableName
     * @return array(string=>ezcDbSchemaField)
     */
    protected function fetchTableFields( $tableName )
    {
        $fields = array();

        $resultArray = $this->db->query( "PRAGMA TABLE_INFO( '$tableName' )" );
        $resultArray->setFetchMode( PDO::FETCH_NUM );

        foreach ( $resultArray as $row )
        {
            $fieldLength = false;
            $fieldPrecision = null;
            $fieldType = self::convertToGenericType( $row[2], $fieldLength, $fieldPrecision );

            $fieldNotNull = false;
            if ( ( $row[3] == '99' ) ||
                 ( $row[3] == '1' ) )
            {
                $fieldNotNull = true;
            }

            $fieldDefault = null;
            if ( $row[4] != '' )
            {
                $fieldDefault = $row[4];
                if ( $fieldType == 'text' )
                {
                    // strip enclosing single quotes if needed
                    if ( $fieldDefault[0] == "'" && substr( $fieldDefault, -1 ) == "'" )
                    {
                        $fieldDefault = substr( $fieldDefault, 1, -1 );
                    }
                }
                if ( $fieldType == 'integer' )
                {
                    $fieldDefault = (int) $fieldDefault;
                }
                if ( $fieldType == 'boolean' )
                {
                    $fieldDefault = $fieldDefault ? 'true' : 'false';
                }
            }

            $fieldAutoIncrement = false;

            if ( $row[5] =='1' )
            {
                $fieldAutoIncrement = true;
                $fieldDefault = null;
            }

            // FIXME: unsigned needs to be implemented
            $fieldUnsigned = false;

            $fields[$row[1]] = ezcDbSchema::createNewField( $fieldType, $fieldLength, $fieldNotNull, $fieldDefault, $fieldAutoIncrement, $fieldUnsigned );
        }

        return $fields;
    }

    /**
     * Converts the native SQLite type in $typeString to a generic DbSchema type.
     *
     * This method converts a string like "float(5,10)" to the generic DbSchema
     * type and uses the by-reference parameters $typeLength and $typePrecision
     * to communicate the optional length and precision of the field's type.
     *
     * @param string  $typeString
     * @param int    &$typeLength
     * @param int    &$typePrecision
     * @return string
     */
    static function convertToGenericType( $typeString, &$typeLength, &$typePrecision )
    {
        preg_match( "@([a-z ]*)(\((\d*)(,(\d+))?\))?@", strtolower( $typeString ), $matches );
        if ( !isset( self::$typeMap[$matches[1]] ) )
        {
            throw new ezcDbSchemaUnsupportedTypeException( 'SQLite', $matches[1] );
        }
        $genericType = self::$typeMap[$matches[1]];

        if ( in_array( $genericType, array( 'text', 'decimal', 'float', 'integer' ) ) && isset( $matches[3] ) )
        {
            $typeLength = $matches[3];
            if ( is_numeric( $typeLength ) )
            {
                $typeLength = (int) $typeLength;
            }
        }
        if ( in_array( $genericType, array( 'decimal', 'float' ) ) && isset( $matches[5] ) )
        {
            $typePrecision = $matches[5];
        }
        if ( $genericType == 'integer' && $typeLength == 1)
        {
            $genericType = 'boolean';
            $typeLength = null;
        }

        return $genericType;
    }

    /**
     * Returns whether the type $type is a numeric type
     *
     * @param string $type
     * @return bool
     */
    private function isNumericType( $type )
    {
        $types = array( 'real', 'integer' );
        return in_array( $type, $types );
    }

    /**
     * Returns whether the type $type is a string type
     *
     * @param string $type
     * @return bool
     */
    private function isStringType( $type )
    {
        $types = array( 'text' );
        return in_array( $type, $types );
    }

    /**
     * Returns whether the type $type is a blob type
     *
     * @param string $type
     * @return bool
     */
    private function isBlobType( $type )
    {
        $types = array( 'blob' );
        return in_array( $type, $types );
    }


    /**
     * Loops over all the indexes in the table $table and extracts information.
     *
     * This method extracts information about the table $tableName's indexes
     * from the database and returns this schema as an array of
     * ezcDbSchemaIndex objects. The key in the array is the index' name.
     *
     * @param  string $tableName
     * @return array(string=>ezcDbSchemaIndex)
     */
    protected function fetchTableIndexes( $tableName )
    {
        $indexBuffer = array();

        $indexNamesArray = $this->db->query( "PRAGMA INDEX_LIST ('$tableName')" );

        $primaryFound = false;

        foreach ( $indexNamesArray as $row )
        {
            $keyName = $row['1'];
            if ( $keyName == $tableName.'_pri' ) 
            {
                $keyName = 'primary';
                $indexBuffer[$keyName]['primary'] = true;
                $indexBuffer[$keyName]['unique'] = true;
                $primaryFound = true;
            }
            else
            {
                $indexBuffer[$keyName]['primary'] = false;
                $indexBuffer[$keyName]['unique'] = $row[2]?true:false;
            }

            $indexArray = $this->db->query( "PRAGMA INDEX_INFO ( '{$row[1]}' )" );

            foreach ( $indexArray as $indexColumnRow )
            {
                $indexBuffer[$keyName]['fields'][$indexColumnRow[2]] = ezcDbSchema::createNewIndexField();
            }
        }

        // search primary index
        $fieldArray = $this->db->query( "PRAGMA TABLE_INFO ('$tableName')" );
        foreach ( $fieldArray as $row )
        {
            if ( $row[5] == '1' ) 
            {
                $keyName = 'primary';
                $indexBuffer[$keyName]['primary'] = true;
                $indexBuffer[$keyName]['unique'] = true;
                $indexBuffer[$keyName]['fields'][$row[1]] = ezcDbSchema::createNewIndexField();
            }
        }

        $indexes = array();

        foreach ( $indexBuffer as $indexName => $indexInfo )
        {
            $indexes[$indexName] = ezcDbSchema::createNewIndex( $indexInfo['fields'], $indexInfo['primary'], $indexInfo['unique'] );
        }

        return $indexes;
    }

}
?>
