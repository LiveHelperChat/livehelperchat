<?php
/**
 * File containing the ezcMvcResultStatusObject class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * The interface that should be implemented by all special status objects.
 *
 * Statis objects are used to specify non-normal results from actions.
 * As an example that could be a "Authorization Required" status, an external
 * redirect etc.
 *
 *
 * @package MvcTools
 * @version 1.1.3
 */
interface ezcMvcResultStatusObject
{
    /**
     * This method is called by the response writers to process the data
     * contained in the status objects.
     *
     * The process method it responsible for undertaking the proper action
     * depending on which response writer is used.
     *
     * @param ezcMvcResponseWriter $writer
     */
    public function process( ezcMvcResponseWriter $writer );
}
?>
