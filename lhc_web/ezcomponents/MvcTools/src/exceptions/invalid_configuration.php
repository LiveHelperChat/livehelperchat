<?php
/**
 * File containing the ezcMvcInvalidConfiguration eception
 *
 * @package MvcTools
 * @version 1.1.3
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception that is thrown if an invalid class is returned from any of the
 * configuration methods of the ezcMvcConfigurableDispatcher configuration.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcInvalidConfiguration extends ezcMvcToolsException
{
    /**
     * Constructs a new ezcMvcInvalidConfiguration exception for configuration $item
     *
     * @param string $item
     * @param mixed  $real
     * @param string $expected
     * @return void
     */
    function __construct( $item, $real, $expected )
    {
        $type = gettype( $real );
        if ( $type == 'object' )
        {
            $type = 'instance of class ' . get_class( $real );
        }
        parent::__construct( "The configuration returned an invalid object for '{$item}', {$expected} expected, but {$type} found." );
    }
}
?>
