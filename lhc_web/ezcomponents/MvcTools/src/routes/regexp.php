<?php
/**
 * File containing the ezcMvcRegexpRoute class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Router class that uses regular expressions for matching routes.
 *
 * The routes are matched against the uri property of the request object.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
class ezcMvcRegexpRoute implements ezcMvcRoute
{
    /**
     * This property contains the regular expression.
     *
     * @var string
     */
    protected $pattern;

    /**
     * This is the name of the controller class that will be instantiated with the
     * request variables obtained from the route, as well as the default values
     * belonging to a route.
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
     * The default values for the variables that are send to the controller. The route
     * matchers can override those default values
     *
     * @var array(string)
     */
    protected $defaultValues;

    /**
     * Constructs a new ezcMvcRegexpRoute with $pattern.
     *
     * When the route is matched (with the match() method), the route instantiates
     * an object of the class $controllerClassName.
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
     * The method first runs the match. If the regular expression matches, it
     * cleans up the variables to only include named parameters.  it then
     * creates an object containing routing information and returns it. If the
     * route's pattern did not match it returns null.
     *
     * @param ezcMvcRequest $request
     * @return null|ezcMvcRoutingInformation
     */
    public function matches( ezcMvcRequest $request )
    {
        if ( $this->pregMatch( $request, $matches ) )
        {
            foreach ( $matches as $key => $match )
            {
                if ( is_numeric( $key ) )
                {
                    unset( $matches[$key] );
                }
            }

            $request->variables = array_merge( $this->defaultValues, $request->variables, $matches );

            return new ezcMvcRoutingInformation( $this->pattern, $this->controllerClassName, $this->action );
        }
        return null;
    }

    /**
     * This method performs the actual regular expresion match against the
     * $request's URI.
     *
     * @param ezcMvcRequest $request
     * @param array(string) $matches
     * @return bool
     */
    protected function pregMatch( $request, &$matches )
    {
        return preg_match( $this->pattern, $this->getUriString( $request ), $matches );
    }

    /**
     * Parses the pattern and adds the prefix.
     *
     * It's up to the developer to provide a meaningfull prefix. In this case,
     * it needs to be a regular expression just like the pattern.
     *
     * @param mixed $prefix
     * @throws ezcMvcRegexpRouteException if the prefix can not be prepended to
     *         the pattern.
     */
    public function prefix( $prefix )
    {
        $pattern = $this->pattern;
        // Find pattern delimiter
        $patternDelim = $pattern[0];
        // Obtain pattern modifiers
        $patternModifier = substr( strrchr( $pattern, $patternDelim ), 1 );
        // Find prefix delimiter
        $prefixDelim = $prefix[0];
        // Obtain prefix modifiers
        $prefixModifier = substr( strrchr( $prefix, $prefixDelim ), 1 );
        // If modifiers are not the same, throw exception
        if ( $patternModifier !== $prefixModifier )
        {
            throw new ezcMvcRegexpRouteException( "The pattern modifiers of the prefix '{$prefix}' and pattern '{$pattern}' do not match." );
        }
        // Reassemble the new pattern
        $newPattern = $patternDelim;
        $newPattern .= substr( $prefix, 1, -1 - strlen( $prefixModifier ) );
        $newPattern .= substr( $pattern, 1 + ( $pattern[1] == '^' ) );

        $this->pattern = $newPattern;
    }
}
?>
