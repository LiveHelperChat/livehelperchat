<?php
/**
 * File containing the ezcPersistentObjectProperty class.
 *
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @package PersistentObject
 */

/**
 * Defines a persistent object field.
 *
 * An instance of this class is used in a {@link ezcPersisentObjectDefinition}
 * object to define a relation between an object property and a database
 * column.
 *
 * @see ezcPersisentObjectDefinition
 *
 * @property string $columnName
 *           The name of the database field that stores the value.
 * @property string $propertyName
 *           The name of the PersistentObject property that holds the value in
 *           the PHP object.
 * @property int $propertyType 
 *           The type of the PHP property. See class constants PHP_TYPE_*.
 * @property ezcPersistentPropertyConverter|null $converter
 *           A converter object that will automatically perform converters on
 *           load and save of a property value.
 * @property int $databaseType
 *           Type of the database column, as defined by PDO constants: {@link
 *           PDO::PARAM_BOOL}, {@link PDO::PARAM_INT}, {@link PDO::PARAM_STR}
 *           (default as defined by {@link ezcQuery::bindValue()}) or {@link
 *           PDO::PARAM_LOB} (important for binary data).
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentObjectProperty
{

    const PHP_TYPE_STRING = 1;
    const PHP_TYPE_INT    = 2;
    const PHP_TYPE_FLOAT  = 3;
    const PHP_TYPE_ARRAY  = 4;
    const PHP_TYPE_OBJECT = 5;
    const PHP_TYPE_BOOL   = 6;

    /**
     * Holds the properties for this class.
     *
     * @var array
     */
    private $properties = array(
        'columnName'       => null,
        'resultColumnName' => null,
        'propertyName'     => null,
        'propertyType'     => self::PHP_TYPE_STRING,
        'converter'        => null,
        'databaseType'     => PDO::PARAM_STR,
    );

    /**
     * Creates a new property definition object.
     *
     * Creates a new property definition object from the given values. The
     * $columnName refers to the name of the database column that, the
     * $propertyName to the name of the PHP object property it refers to.
     * The $type defines the type of the resulting PHP property, the database
     * value will be casted accordingly after load.
     *
     * @param string $columnName
     * @param string $propertyName
     * @param int $type
     * @param ezcPersistentPropertyConverter $converter
     * @param int $databaseType
     */
    public function __construct(
        $columnName   = null,
        $propertyName = null,
        $type         = self::PHP_TYPE_STRING,
        $converter    = null,
        $databaseType = PDO::PARAM_STR
    )
    {
        $this->columnName   = $columnName;
        $this->propertyName = $propertyName;
        $this->propertyType = $type;
        $this->converter    = $converter;
        $this->databaseType = $databaseType;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcPersistentObjectProperty
     */
    public static function __set_state( array $array )
    {
        if ( isset( $array['properties'] ) )
        {
            return new ezcPersistentObjectProperty(
                $array['properties']['columnName'],
                $array['properties']['propertyName'],
                $array['properties']['propertyType'],
                ( isset( $array['properties']['converter'] ) ? $array['properties']['converter'] : null ),
                ( isset( $array['properties']['databaseType'] ) ? $array['properties']['databaseType'] : PDO::PARAM_STR )
            );
        }
        else
        {
            // Old style exports
            return new ezcPersistentObjectProperty(
                $array['columnName'],
                $array['propertyName'],
                $array['propertyType'],
                ( isset( $array['converter'] ) ? $array['converter'] : null ),
                ( isset( $array['databaseType'] ) ? $array['databaseType'] : PDO::PARAM_STR )
            );
        }
    }

    /**
     * Property read access.
     *
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     * @ignore
     */
    public function __get( $propertyName )
    {
        if ( $this->__isset( $propertyName ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property write access.
     *
     * @param string $propertyName Name of the property.
     * @param mixed $propertyValue  The value for the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If a the value for the property options is not an instance of
     * @throws ezcBaseValueException
     *         If a the value for a property is out of range.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'columnName':
                if ( is_string( $propertyValue ) === false && is_null( $propertyValue ) === false )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'string or null'
                    );
                }
                $this->properties['resultColumnName'] = ( $propertyValue !== null ) ? strtolower( $propertyValue ) : null;
                break;
            case 'propertyName':
                if ( is_string( $propertyValue ) === false && is_null( $propertyValue ) === false )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'string or null'
                    );
                }
                break;
            case 'propertyType':
                if ( is_int( $propertyValue ) === false && is_null( $propertyValue ) === false )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'int or null'
                    );
                }
                break;
            case 'converter':
                if ( !( $propertyValue instanceof ezcPersistentPropertyConverter ) && !is_null( $propertyValue ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcPersistentPropertyConverter or null'
                    );
                }
                break;
            case 'databaseType':
                if ( $propertyValue !== PDO::PARAM_STR && $propertyValue !== PDO::PARAM_LOB && $propertyValue !== PDO::PARAM_INT && $propertyValue !== PDO::PARAM_BOOL )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'PDO::PARAM_STR, PDO::PARAM_LOB, PDO::PARAM_INT or PDO::PARAM_BOOL'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
                break;
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property isset access.
     *
     * @param string $propertyName Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }


    /**
     * @apichange Never used but left for BC reasons. Will be removed on next
     *            major version.
     */
    const VISIBILITY_PRIVATE = 1;

    /**
     * @apichange Never used but left for BC reasons. Will be removed on next 
     *            major version.
     */
    const VISIBILITY_PROTECTED = 2;

    /**
     * @apichange Never used but left for BC reasons. Will be removed on next 
     *            major version.
     */
    const VISIBILITY_PUBLIC  = 3;

    /**
     * @apichange Never used but left for BC reasons. Will be removed on next 
     *            major version.
     */
    const VISIBILITY_PROPERTY = 4;
}
?>
