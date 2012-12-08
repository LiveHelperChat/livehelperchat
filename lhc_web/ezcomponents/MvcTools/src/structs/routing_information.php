<?php
/**
 * File containing the ezcMvcRoutingInformation class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * This struct contains information from the router that belongs to the matched
 * route.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcRoutingInformation extends ezcBaseStruct
{
    /**
     * Contains the pattern of the matched route, to be used for view matching
     * and filter chain selection.
     *
     * @var string
     */
    public $matchedRoute;

    /**
     * Contains the class name of the controller that should be instantiated
     * for this route.
     *
     * @var string
     */
    public $controllerClass;

    /**
     * Contains the action that the controller should run.
     *
     * @var string
     */
    public $action;

    /**
     * Contains a backlink to the router, so that the dispatcher can pass this
     * on to the created controllers.
     *
     * @var ezcMvcRouter
     */
    public $router;

    /**
     * Constructs a new ezcMvcRoutingInformation.
     *
     * @param string $matchedRoute
     * @param string $controllerClass
     * @param string $action
     * @param ezcMvcRouter $router
     */
    public function __construct( $matchedRoute = '', $controllerClass = '', $action = '', ezcMvcRouter $router = null )
    {
        $this->matchedRoute = $matchedRoute;
        $this->controllerClass = $controllerClass;
        $this->action = $action;
        $this->router = $router;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcMvcRoutingInformation
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcRoutingInformation( $array['matchedRoute'],
            $array['controllerClass'], $array['action'], $array['router'] );
    }
}
?>
