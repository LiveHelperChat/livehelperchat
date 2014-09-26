<?php
/**
 * File containing the ezcPersistentUndeterministicRelationException class
 *
 * @package PersistentObject
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown, if an operation on a multi-relation class missed the relation name.
 *
 * @package PersistentObject
 * @version 1.7.1
 */
class ezcPersistentUndeterministicRelationException extends ezcPersistentObjectException
{

    /**
     * Constructs a new ezcPersistentUndeterministicRelationException for the given
     * relation class $class.
     *
     * @param string $class
     * @return void
     */
    public function __construct( $class )
    {
        parent::__construct( "There are multiple relations defined for class $class, but the neccessary relation name was missing for the desired operation." );
    }
}
?>
