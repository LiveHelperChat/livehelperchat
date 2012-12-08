<?php
/**
 * File containing the ezcMvcRailsRoute class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Router class that uses rails style expressions for matching routes.
 *
 * The routes are matched against the uri property of the request object. The
 * matching algorithm works as follows:
 *
 * - The pattern and URI as returned by getUriString() are split on the /
 *   character.
 * - If the first part of the split *pattern* contains a "." (dot) then the
 *   first part of the pattern and the URI are split by ".". The return of
 *   this, together with the rest of the original split-by-slash string are
 *   concatenated.
 * - Each of the two arrays are compared with each other with the delimiters
 *   being ignored.
 * - A special case are elements in the pattern that start with a ":". In this
 *   case the pattern element and uri element do not need to match. Instead the
 *   pattern element creates a named variable with as value the element from
 *   the URI array with the same index.
 * - If not every element matches, the route does not match and
 *   false is returned. If everything matches, true is returned.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
class ezcMvcRailsRoute implements ezcMvcRoute, ezcMvcReversibleRoute
{
    /**
     * This property contains the pattern
     *
     * @var string
     */
    protected $pattern;

    /**
     * This is the name of the controller class that will be instantiated with
     * the request variables obtained from the route, as well as the default
     * values belonging to a route.
     *
     * @var string
     */
    protected $controllerClassName;

    /**
     * Contains the action that the controller should execute.
     *
     * @var string
     */
    protected $action;

    /**
     * The default values for the variables that are send to the controller.
     * The route matchers can override those default values
     *
     * @var array(string)
     */
    protected $defaultValues;

    /**
     * Constructs a new ezcMvcRailsRoute with $pattern.
     *
     * When the route is matched (with the match() method), the route
     * instantiates an object of the class $controllerClassName.
     *
     * @param string $pattern
     * @param string $controllerClassName
     * @param string $action
     * @param array(string) $defaultValues
     */
    public function __construct( $pattern, $controllerClassName, $action = null, array $defaultValues = array() )
    {
        $this->pattern = $pattern;
        $this->controllerClassName = $controllerClassName;
        $this->action = $action;
        $this->defaultValues = $defaultValues;
    }

    /**
     * Returns the request information that the matches() method will match the
     * pattern against.
     *
     * @param ezcMvcRequest $request
     * @return string
     */
    protected function getUriString( ezcMvcRequest $request )
    {
        return $request->uri;
    }

    /**
     * Evaluates the URI against this route.
     *
     * The method first runs the match. If the pattern matches, it then creates
     * an object containing routing information and returns it. If the route's
     * pattern did not match it returns null.
     *
     * @param ezcMvcRequest $request
     * @return null|ezcMvcRoutingInformation
     */
    public function matches( ezcMvcRequest $request )
    {
        if ( $this->match( $request, $matches ) )
        {
            $request->variables = array_merge( $this->defaultValues, $request->variables, $matches );

            return new ezcMvcRoutingInformation( $this->pattern, $this->controllerClassName, $this->action );
        }
        return null;
    }

    /**
     * This method performs the actual pattern match against the $request's
     * URI.
     *
     * @param ezcMvcRequest $request
     * @param array(string) $matches
     * @return bool
     */
    protected function match( $request, &$matches )
    {
        $matches = array();

        // first we split the pattern and request ID per /
        $patternParts = preg_split( '@(/)@', $this->pattern, null, PREG_SPLIT_DELIM_CAPTURE );
        $requestParts = preg_split( '@(/)@', $this->getUriString( $request ), null, PREG_SPLIT_DELIM_CAPTURE );

        if ( strpos( $patternParts[0], '.' ) !== false )
        {
            $subPatternParts = preg_split( '@([.])@', $patternParts[0], null, PREG_SPLIT_DELIM_CAPTURE );
            $subRequestParts = preg_split( '@([.])@', $requestParts[0], null, PREG_SPLIT_DELIM_CAPTURE );

            $patternParts = array_merge( $subPatternParts, array_slice( $patternParts, 1 ) );
            $requestParts = array_merge( $subRequestParts, array_slice( $requestParts, 1 ) );
        }

        // if the number of / is not the same, it can not match
        if ( count( $patternParts ) != count( $requestParts ) )
        {
            return false;
        }

        // now loop over all parts of the pattern, and see if it matches with
        // the request URI
        foreach ( $patternParts as $id => $patternPart )
        {
            if ( $patternPart == '' || $patternPart[0] != ':' )
            {
                if ( $patternPart !== $requestParts[$id] )
                {
                    return false;
                }
            }
            else
            {
                if ( $requestParts[$id] == '' )
                {
                    return false;
                }
                $matches[substr( $patternPart, 1 )] = $requestParts[$id];
            }
        }
        return true;
    }

    /**
     * Adds the $prefix to the route's pattern.
     *
     * It's up to the developer to provide a meaningfull prefix. In this case,
     * it needs to be a pattern just like the normal pattern.
     *
     * @param mixed $prefix
     */
    public function prefix( $prefix )
    {
        $this->pattern = $prefix . $this->pattern;
    }

    /**
     * Generates an URL back out of a route, including possible arguments
     *
     * @param array $arguments
     */
    public function generateUrl( array $arguments = null )
    {
        $patternParts = explode( '/', $this->pattern );
        foreach ( $patternParts as &$part )
        {
            if ( strlen( $part ) > 1 && $part[0] === ':' )
            {
                $paramName = substr( $part, 1 );
                if ( !isset( $arguments[$paramName] ) )
                {
                    throw new ezcMvcMissingRouteArgumentException( $this->pattern, $paramName );
                }
                else
                {
                    $part = $arguments[$paramName];
                }
            }
        }
        return join( '/', $patternParts );
    }
}
?>
