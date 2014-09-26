<?php
/**
 * File containing the ezcPersistentObjectDefinition class.
 *
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @package PersistentObject
 */
/**
 * Main definition of a persistent object.
 *
 * Each persistent object will have exactly one definition. The purpose of
 * the definition is to provide information about how the database table is structured
 * and how it is mapped to the data object.
 *
 * For an elaborate example see {@link ezcPersistentSession}.
 *
 * @package PersistentObject
 * @version 1.7.1
 * @mainclass
 *
 * @property string $table Name of the database table to use.
 * @property string $class Class-name of the PersistentObject.
 * @property ezcPersistentObjectIdProperty $idProperty Holds the identifier property.
 * @property array(string=>ezcPersistentObjectProperty) $properties
 *           The fields of the Persistent Object as an array of
 *           ezcPersistentObjectProperty.
 * @property array(string=>ezcPersistentObjectProperty) $columns
 *           The fields of the Persistent Object as an array of
 *           ezcPersistentObjectProperty.  The key is the name of the original
 *           database column.
 * @property array(string=>ezcPersistentRelation) $relations
 *           Contains the relations of this object. An array indexed by class
 *           names of the related object, assigned to a instance of a class
 *           derived from ezcPersistentRelation.
 */
class ezcPersistentObjectDefinition
{

    /**
     * Property array.
     * Named differently to avoid problems with the property $properties.
     * 
     * @var array(string=>mixed)
     */
    protected $propertyArray = array(
        'table'      => null,
        'class'      => null,
        'idProperty' => null,
        'properties' => null,
        'columns'    => null,
        'relations'  => null,
    );

    /**
     * Constructs a new PersistentObjectDefinition.
     *
     * @param string $table The name of the database table to map to.
     * @param string $class The name of the PHP class to map to.
     * @param array $properties The properties of the class. See {@link $properties}
     * @param array $relations The relations of the class. See {@link $relations}
     * @param ezcPersistentObjectIdProperty $idProperty The primary key of the class/table.
     */
    public function __construct( $table = '',
                                 $class = '',
                                 array $properties = array(),
                                 array $relations = array(),
                                 ezcPersistentObjectIdProperty $idProperty = null )
    {
        $this->table      = $table;
        $this->class      = $class;
        $this->idProperty = $idProperty;
        
        $this->relations = new ezcPersistentObjectRelations();
        $this->relations->exchangeArray( $relations );

        $this->propertyArray['properties'] = new ezcPersistentObjectProperties();
        $this->properties->exchangeArray( $properties );

        $this->propertyArray['columns'] = new ezcPersistentObjectColumns();
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
            return $this->propertyArray[$propertyName];
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
            case 'class':
            case 'table':
                if ( is_string( $propertyValue ) === false && is_null( $propertyValue ) === false )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'string or null'
                    );
                }
                break;
            case 'idProperty':
                if ( ( is_object( $propertyValue ) === false || ( $propertyValue instanceof ezcPersistentObjectIdProperty ) === false ) && $propertyValue !== null )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'ezcPersistentObjectIdProperty'
                    );
                }
                break;
            case 'properties':
                if ( is_array( $propertyValue ) === false && ( is_object( $propertyValue ) === false || ( $propertyValue instanceof ezcPersistentObjectProperties ) === false ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        // Left with array by intention. ezcPersistentObjectRelations is not public.
                        'array'
                    );
                }
                if ( is_array( $propertyValue ) )
                {
                    $this->propertyArray[$propertyName]->exchangeArray( $propertyValue );
                    return;
                }
                break;
            case 'columns':
                if ( is_array( $propertyValue ) === false && ( is_object( $propertyValue ) === false || ( $propertyValue instanceof ezcPersistentObjectColumns ) === false ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        // Left with array by intention. ezcPersistentObjectRelations is not public.
                        'array'
                    );
                }
                if ( is_array( $propertyValue ) )
                {
                    $this->propertyArray[$propertyName]->exchangeArray( $propertyValue );
                    return;
                }
                break;
            case 'relations':
                if ( is_array( $propertyValue ) === false && ( is_object( $propertyValue ) === false || ( $propertyValue instanceof ezcPersistentObjectRelations ) === false ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        // Left with array by intention. ezcPersistentObjectRelations is not public.
                        'array'
                    );
                }
                if ( is_array( $propertyValue ) )
                {
                    $this->propertyArray[$propertyName]->exchangeArray( $propertyValue );
                    return;
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
                break;
        }
        $this->propertyArray[$propertyName] = $propertyValue;
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
        return array_key_exists( $propertyName, $this->propertyArray );
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
     * @return ezcPersistentObjectDefinition
     */
    public static function __set_state( array $array )
    {
        if ( isset( $array['propertyArray'] ) && count( $array ) === 1 )
        {
            $def =  new ezcPersistentObjectDefinition(
                $array['propertyArray']['table'],
                $array['propertyArray']['class']
            );
            $def->properties = $array['propertyArray']['properties'];
            $def->columns    = $array['propertyArray']['columns'];
            $def->relations  = $array['propertyArray']['relations'];
            $def->idProperty = $array['propertyArray']['idProperty'];
        }
        else
        {
            $def = new ezcPersistentObjectDefinition(
                $array['table'],
                $array['class'],
                $array['properties'],
                $array['relations'],
                $array['idProperty']
            );
            $def->columns = $array['columns'];
        }
        return $def;
    }
}
?>
