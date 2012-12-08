<?php
/**
 * This file contains the ezcWorkflowExecutionVisualizerPluginOptions class.
 *
 * @package Workflow
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Options class for ezcWorkflowExecutionVisualizerPlugin.
 *
 * @property string $directory
 *           The directory to which the DOT files are written.
 * @property bool $includeVariables
 *           Whether or not to include workflow variables.
 * @package Workflow
 * @version 1.4.1
 */
class ezcWorkflowExecutionVisualizerPluginOptions extends ezcBaseOptions
{
    /**
     * Properties.
     *
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'directory'        => null,
        'includeVariables' => true,
    );

    /**
     * Property write access.
     *
     * @param string $propertyName  Name of the property.
     * @param mixed  $propertyValue The value for the property.
     *
     * @throws ezcBasePropertyNotFoundException
     *         If the the desired property is not found.
     * @throws ezcBaseFileNotFoundException
     *         When the directory does not exist.
     * @throws ezcBaseFilePermissionException
     *         When the directory is not writable.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'directory':
                if ( !is_string( $propertyValue ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'string'
                    );
                }

                if ( !is_dir( $propertyValue ) )
                {
                    throw new ezcBaseFileNotFoundException( $propertyValue, 'directory' );
                }

                if ( !is_writable( $propertyValue ) )
                {
                    // @codeCoverageIgnoreStart
                    throw new ezcBaseFilePermissionException( $propertyValue, ezcBaseFileException::WRITE );
                    // @codeCoverageIgnoreEnd
                }
                break;
            case 'includeVariables':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'bool'
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
