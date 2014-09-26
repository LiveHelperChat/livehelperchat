<?php
/**
 * File containing the ezcAuthenticationStatus class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Holds the statuses returned from each authentication filter.
 *
 * @package Authentication
 * @version 1.3.1
 */
class ezcAuthenticationStatus
{
    /**
     * Holds the statuses returned by the authentication filters.
     *
     * var array(string=>mixed)
     */
    private $statuses = array();

    /**
     * Adds a new status to the list of statuses.
     *
     * @param string $class The class name associated with the status
     * @param mixed|array(mixed) $status A status associated with the class name
     */
    public function append( $class, $status )
    {
        if ( is_array( $status ) )
        {
            $this->statuses = array_merge( $this->statuses, $status );
        }
        else
        {
            $this->statuses[] = array( $class => $status );
        }
    }

    /**
     * Returns the list of authentication statuses.
     *
     * The format of the returned array is array( class => code ).
     *
     * Example:
     * <code>
     * array(
     * 'ezcAuthenticationSession' => ezcAuthenticationSession::STATUS_EMPTY,
     * 'ezcAuthenticationDatabaseFilter' => ezcAuthenticationDatabaseFilter::STATUS_PASSWORD_INCORRECT
     *      );
     * </code>
     *
     * @return array(string=>mixed)
     */
    public function get()
    {
        return $this->statuses;
    }
}
?>
