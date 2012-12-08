<?php
/**
 * File containing the ezcAuthenticationDataFetch interface.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Interface defining functionality for fetching extra data during the
 * authentication process.
 *
 * Authentication filters which support fetching additional data during
 * the authentication process can implement this interface.
 *
 * @package Authentication
 * @version 1.3.1
 */
interface ezcAuthenticationDataFetch
{
    /**
     * Registers which extra data to fetch during the authentication process.
     *
     * The input $data should be an array of attributes to request, for example:
     * <code>
     * array( 'name', 'company', 'mobile' );
     * </code>
     *
     * The extra data that is possible to return depends on the authentication
     * filter. Please read the description of each filter to find out what extra
     * data is possible to fetch.
     *
     * @param array(string) $data A list of attributes to fetch during authentication
     */
    public function registerFetchData( array $data = array() );

    /**
     * Returns the extra data fetched during the authentication process.
     *
     * The return is something like this:
     * <code>
     * array( 'name' = > array( 'Dr. No' ),
     *        'company' => array( 'SPECTRE' ),
     *        'mobile' => array( '555-7732873' )
     *      );
     * </code>
     *
     * The extra data that is possible to return depends on the authentication
     * filter. Please read the description of each filter to find out what extra
     * data is possible to fetch.
     *
     * @return array(string=>mixed)
     */
    public function fetchData();
}
?>
