<?php
/**
 * File containing the ezcAuthenticationFilter class.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 * @package Authentication
 * @version 1.3.1
 */

/**
 * Base class for all authentication filters.
 *
 * The classes which extend this class must implement the run() method.
 *
 * This class contains the STATUS_OK constant (with value 0) which is returned
 * by the run() method in case of success. Subclasses must define their own
 * constants to be returned in case of insuccess.
 *
 * This class adds support for options for subclasses, by providing the protected
 * property $options, and the public methods setOptions() and getOptions().
 *
 * @package Authentication
 * @version 1.3.1
 */
abstract class ezcAuthenticationFilter
{
    /**
     * Successful authentication.
     */
    const STATUS_OK = 0;

    /**
     * Options for authentication filters.
     * 
     * @var ezcAuthenticationFilterOptions
     */
    protected $options;

    /**
     * Sets the options of this class to $options.
     *
     * @param ezcAuthenticationFilterOptions $options Options for this class
     */
    public function setOptions( ezcAuthenticationFilterOptions $options )
    {
        $this->options = $options;
    }

    /**
     * Returns the options of this class.
     *
     * @return ezcAuthenticationFilterOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Runs the filter and returns a status code when finished.
     *
     * @param ezcAuthenticationCredentials $credentials Authentication credentials
     * @return int
     */
    abstract public function run( $credentials );
}
?>
