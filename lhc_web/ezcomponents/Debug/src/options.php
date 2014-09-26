<?php
/**
 * This file contains the ezcDebugOptions class.
 *
 * @package Debug
 * @version 1.2.1
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Options class for ezcDebug.
 *
 * @property bool $stackTrace
 *           Determines if a stack trace is stored and displayed with every
 *           debug message by default. Default is false.
 * @property int $stackTraceDepth
 *           The number of levels to store for the stack trace. 0 means no
 *           limit and a complete stack trace will always be stored. Not that
 *           this might consume a large amount of memory. Default is 5.
 * @property int $stackTraceMaxData
 *           Maximum bytes of data to print per variable in a stacktrace. This
 *           option does only affect {@link ezcDebugPhpStacktraceIterator},
 *           {@link ezcDebugXdebugStacktraceIterator} is configured by the
 *           Xdebug INI settings. Default is 512. Set to false to show all data.
 * @property int $stackTraceMaxChildren
 *           Maximum number of children to dump on 1 level in array and object
 *           structures. This option does only affect {@link
 *           ezcDebugPhpStacktraceIterator}, {@link
 *           ezcDebugXdebugStacktraceIterator} is configured by the Xdebug INI
 *           settings. Default is 128. Set to false to show all children.
 * @property int $stackTraceMaxDepth
 *           Maximum depth to recurse into array and object structures in a
 *           stack trace. This option does only affect {@link
 *           ezcDebugPhpStacktraceIterator}, {@link
 *           ezcDebugXdebugStacktraceIterator} is configured by the Xdebug INI
 *           settings. Default is 3. Set to false to recurse fully.
 *
 * @package Debug
 * @version 1.2.1
 */
class ezcDebugOptions extends ezcBaseOptions
{
    /**
     * Properties.
     * 
     * @var array(string=>mixed)
     */
    protected $properties = array(
        'stackTrace'            => false,
        'stackTraceDepth'       => 5,
        'stackTraceMaxData'     => 512,
        'stackTraceMaxChildren' => 128,
        'stackTraceMaxDepth'    => 3,
    );

    /**
     * Property write access.
     * 
     * @param string $propertyName Name of the property.
     * @param mixed $propertyValue The value for the property.
     *
     * @throws ezcBasePropertyPermissionException
     *         If the property you try to access is read-only.
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case 'stackTrace':
                if ( !is_bool( $propertyValue ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'bool'
                    );
                }
                break;
            case 'stackTraceDepth':
                if ( !is_int( $propertyValue ) || $propertyValue < 0 )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'int >= 0'
                    );
                }
                break;
            case 'stackTraceMaxData':
            case 'stackTraceMaxChildren':
            case 'stackTraceMaxDepth':
                if ( ( !is_int( $propertyValue ) || $propertyValue < 0 ) && ( $propertyValue !== false ) )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        $propertyValue,
                        'int >= 0 or false'
                    );
                }
                break;
            default:
                ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }
}

?>
