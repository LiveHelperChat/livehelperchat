<?php
/**
 * File containing the ezcAuthenticationCredentials structure.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Base class for all authentication credentials structures.
 *
 * @package Authentication
 * @version 1.3.1
 */
abstract class ezcAuthenticationCredentials extends ezcBaseStruct
{
    /**
     * Returns string representation of the credentials.
     *
     * Use it to save the credentials in the session.
     * 
     * @return string
     */
    abstract public function __toString();
}
?>
