<?php
/**
 * File containing the ezcDocumentLocateable interface
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Interface for elements, which have a location ID, and thus can be used by
 * the style inferencer.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
interface ezcDocumentLocateable
{
    /**
     * Get elements location ID
     *
     * Return the elements location ID, based on the factors described in the
     * class header.
     *
     * @return string
     */
    public function getLocationId();
}
?>
