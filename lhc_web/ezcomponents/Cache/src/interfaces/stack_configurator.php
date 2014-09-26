<?php
/**
 * File containing the ezcCacheStackConfigurator interface.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Interface to be implemented by stack configurator classes.
 *
 * To allow the usage of {@link ezcCacheStack} with the {@link
 * ezcCacheManager}, a class implementing this interface is necessary. The name
 * of the class must be stored in the {@link ezcCacheStackOptions} defined for
 * the stack in the manager. As soon as the stack is requested by the user for
 * the first time, a new {@link ezcCacheStack} object will be created in the
 * manager. This object will be given to the {@link
 * ezcCacheStackConfigurator->configure()} method of the class named in the
 * options.
 * 
 * @package Cache
 * @version 1.5
 */
interface ezcCacheStackConfigurator
{
    /**
     * Configures the given stack.
     *
     * This method configures the given $stack object. The object is usually
     * expected to be newly constructed after this method receives it. If given
     * in a class implemnting this interface is given in {@link
     * ezcCacheStackOptions}, this method will be called automatically from
     * {@link ezcCacheStack->__construct()}.
     *
     * This method is expected to use the {@link ezcCacheStack->pushStorage()}
     * method to configure storages in the stack.
     * 
     * @param ezcCacheStack $stack 
     * @return void
     */
    public static function configure( ezcCacheStack $stack );
}

?>
