<?php
/**
 * File containing the ezcDbSchemaOptions class
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */
/**
 * Class containing the basic options for charts
 *
 * @property string $tableClassName
 *                  The objects that are returned for each table are of this
 *                  class, it needs to extend from the ezcDbSchemaTable struct.
 * @property string $fieldClassName
 *                  The objects that are returned for each field are of this
 *                  class, it needs to extend from the ezcDbSchemaField struct.
 * @property string $indexClassName
 *                  The objects that are returned for each index are of this
 *                  class, it needs to extend from the ezcDbSchemaIndex struct.
 * @property string $indexFieldClassName
 *                  The objects that are returned for each index field are of
 *                  this class, it needs to extend from the
 *                  ezcDbSchemaIndexField struct.
 *
 * @version 1.4.4
 * @package DatabaseSchema
 */
class ezcDbSchemaOptions extends ezcBaseOptions
{
    /**
     * Constructor
     * 
     * @param array $options Default option array
     * @return void
     * @ignore
     */
    public function __construct( array $options = array() )
    {
        $this->properties['tableClassName'] = 'ezcDbSchemaTable';
        $this->properties['fieldClassName'] = 'ezcDbSchemaField';
        $this->properties['indexClassName'] = 'ezcDbSchemaIndex';
        $this->properties['indexFieldClassName'] = 'ezcDbSchemaIndexField';
        $this->properties['tableNamePrefix'] = '';
        parent::__construct( $options );
    }

    /**
     * Set an option value
     * 
     * @param string $propertyName 
     * @param mixed $propertyValue 
     * @throws ezcBasePropertyNotFoundException
     *         If a property is not defined in this class
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @throws ezcBaseInvalidParentClassException
     *         if the class name passed as replacement for any of the built-in
     *         classes do not inherit from the built-in classes.
     * @return void
     */
    public function __set( $propertyName, $propertyValue )
    {
        $parentClassMap = array(
            'tableClassName' => 'ezcDbSchemaTable',
            'fieldClassName' => 'ezcDbSchemaField',
            'indexClassName' => 'ezcDbSchemaIndex',
            'indexFieldClassName' => 'ezcDbSchemaIndexField',
        );
        switch ( $propertyName )
        {
            case 'tableClassName':
            case 'fieldClassName':
            case 'indexClassName':
            case 'indexFieldClassName':
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'string that contains a class name' );
                }

                // Check if the passed classname actually implements the
                // correct parent class.
                if ( $parentClassMap[$propertyName] !== $propertyValue && !in_array( $parentClassMap[$propertyName], class_parents( $propertyValue ) ) )
                {
                    throw new ezcBaseInvalidParentClassException( $parentClassMap[$propertyName], $propertyValue );
                }

                $this->properties[$propertyName] = $propertyValue;
                break;

            case 'tableNamePrefix':
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException( $propertyName, $propertyValue, 'string' );
                }
                $this->properties[$propertyName] = $propertyValue;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
                break;
        }
    }
}

?>
