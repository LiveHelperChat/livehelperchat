<?php
/**
 * File containing the ezcMvcResultFilter class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * A result filter is responsible for altering the result object.
 *
 * @package MvcTools
 * @version 1.1.3
 */
interface ezcMvcResultFilter
{
    /**
     * Alters the result object.
     *
     * @param ezcMvcResult $result Result object to alter.
     * @return void
     */
    public function filterResult( ezcMvcResult $result );

    /**
     * Sets options on the filter object
     *
     * @throws ezcMvcFilterHasNoOptionsException if the filter does not support options.
     * @param array $options
     */
    public function setOptions( array $options );
}
?>
