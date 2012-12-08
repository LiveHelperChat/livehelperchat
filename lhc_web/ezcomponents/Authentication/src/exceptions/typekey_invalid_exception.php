<?php
/**
 * File containing the ezcAuthenticationTypekeyPublicKeysInvalidException class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Thrown the public keys file contained invalid data in TypeKey authentication.
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationTypekeyPublicKeysInvalidException extends ezcAuthenticationTypekeyException
{
    /**
     * Constructs a new ezcAuthenticationTypekeyPublicKeysInvalidException
     * with error message $message.
     *
     * @param string $message Message to throw
     */
    public function __construct( $message )
    {
        parent::__construct( $message );
    }
}
?>
