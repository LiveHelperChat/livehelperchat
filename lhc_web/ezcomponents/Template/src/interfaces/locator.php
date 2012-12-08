<?php
/**
 * File containing the ezcTemplateLocator class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * @package Template
 * @version 1.4.2
 *
 * This interface is to be implemented by objects that can act as path
 * translators for template includes.
 */
interface ezcTemplateLocator
{
    /**
     * Method that is called upon every request for a template file.
     *
     * The method is supposed to return a path to the resolved template file.
     *
     * @param string $path
     * @return string
     */
	public function translatePath( $path );
}
?>
