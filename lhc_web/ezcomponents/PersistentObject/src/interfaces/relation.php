<?php
/**
 * File containing the ezcPersistentRelation class.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Base class for all relation definition classes of PersistentObject.
 *
 * @property string $sourceTable
 *           The source table this relation maps from.
 * @property string $destinationTable
 *           The destination table this relation maps to.
 * @property array $columnMap
 *           The table mapping this instance reflects.
 * @property bool $reverse
 *           Wether this relation is a reverse of an existing relation.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
abstract class ezcPersistentRelation
{
    /**
     * Properties array.
     *
     * @var array
     */
    protected $properties = array(
        "sourceTable"       => null,
        "destinationTable"  => null,
        "columnMap"         => array(),
        "reverse"           => false,
    );

    /**
     * Validates an {@link ezcPersistentRelation::$columnMap} property.
     * Checks is the given array represents a valid $columnMap property. Has
     * to be implemented by all derived classes.
     *
     * @param array $columnMap The column map to check.
     *
     * @throws ezcBaseValueException On an invalid column map.
     */
    abstract protected function validateColumnMap( array $columnMap );

    /**
     * Create a new relation.
     *
     * @param string $sourceTable      See property $sourceTable
     * @param string $destinationTable See property $destinationTable
     */
    public function __construct( $sourceTable, $destinationTable )
    {
        $this->sourceTable = $sourceTable;
        $this->destinationTable = $destinationTable;
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
        if ( isset( $this->$propertyName ) )
        {
            return ( is_array( $this->properties[$propertyName] ) ) ? (array) $this->properties[$propertyName] : $this->properties[$propertyName];
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
            case "sourceTable":
            case "destinationTable":
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        "string"
                    );
                }
                $this->properties[$propertyName] = $propertyValue;
                break;
            case "columnMap":
                $this->validateColumnMap( $propertyValue );
                $this->properties["columnMap"] = $propertyValue;
                break;
            case "reverse":
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, "bool" );
                }
                $this->properties["reverse"] = $propertyValue;
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
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
}

?>
