<?php
/**
 * File containing the ezcMvcDispatcher class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Dispatcher interface.
 *
 * A dispatcher is the glue code that binds all the other parts of the MvcTools
 * package together.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
interface ezcMvcDispatcher
{
    /**
     * Runs the dispatcher.
     */
    public function run();
}
?>
