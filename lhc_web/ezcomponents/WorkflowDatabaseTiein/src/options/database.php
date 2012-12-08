<?php
/**
 * This file contains the ezcWorkflowDatabaseOptions class.
 *
 * @package WorkflowDatabaseTiein
 * @version 1.4
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Options class for ezcWorkflowDatabase.
 *
 * @property string $prefix
 *                  The database table name prefix to be used.
 *
 * @package WorkflowDatabaseTiein
 * @version 1.4
 */
class ezcWorkflowDatabaseOptions extends ezcBaseOptions
{
    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'prefix' => '',
    );

    /**
     * Property write access.
     *
     * @param string $propertyName  Name of the property.
     * @param mixed  $propertyValue The value for the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'prefix':
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'string'
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }
}
?>
