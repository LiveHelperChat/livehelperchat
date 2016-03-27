<?php
/**
 * File containing the ezcUrl class.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.2.2
 * @filesource
 * @package Url
 */

/**
 * ezcUrl stores an URL both absolute and relative and contains methods to
 * retrieve the various parts of the URL and to manipulate them.
 *
 * Example of use:
 * <code>
 * // create an ezcUrlConfiguration object
 * $urlCfg = new ezcUrlConfiguration();
 * // set the basedir and script values
 * $urlCfg->basedir = 'mydir';
 * $urlCfg->script = 'index.php';
 *
 * // define delimiters for unordered parameter names
 * $urlCfg->unorderedDelimiters = array( '(', ')' );
 *
 * // define ordered parameters
 * $urlCfg->addOrderedParameter( 'section' );
 * $urlCfg->addOrderedParameter( 'group' );
 * $urlCfg->addOrderedParameter( 'category' );
 * $urlCfg->addOrderedParameter( 'subcategory' );
 *
 * // define unordered parameters
 * $urlCfg->addUnorderedParameter( 'game', ezcUrlConfiguration::MULTIPLE_ARGUMENTS );
 *
 * // create a new ezcUrl object from a string URL and use the above $urlCfg
 * $url = new ezcUrl( 'http://www.example.com/mydir/index.php/groups/Games/Adventure/Adult/(game)/Larry/7', $urlCfg );
 *
 * // to get the parameter values from the URL use $url->getParam():
 * $section =  $url->getParam( 'section' ); // will be "groups"
 * $group = $url->getParam( 'group' ); // will be "Games"
 * $category = $url->getParam( 'category' ); // will be "Adventure"
 * $subcategory = $url->getParam( 'subcategory' ); // will be "Adult"
 * $game = $url->getParam( 'game' ); // will be array( "Larry", "7" )
 * </code>
 *
 * Example of aggregating values for unordered parameters:
 * <code>
 * $urlCfg = new ezcUrlConfiguration();
 *
 * $urlCfg->addUnorderedParameter( 'param1', ezcUrlConfiguration::AGGREGATE_ARGUMENTS );
 * $url = new ezcUrl( 'http://www.example.com/(param1)/x/(param1)/y/z', $urlCfg );
 *
 * $param1 = $url->getParam( 'param1' ); // will be array( array( "x" ), array( "y", "z" ) )
 * </code>
 *
 * Unordered parameters can also be fetched as a flat array (useful if the
 * URL doesn't have delimiters for the unordered parameter names). Example:
 * <code>
 * $urlCfg = new ezcUrlConfiguration();
 * $urlCfg->basedir = '/mydir/shop';
 * $urlCfg->script = 'index.php';
 * $urlCfg->addOrderedParameter( 'module' );
 *
 * $url = new ezcUrl( 'http://www.example.com/mydir/shop/index.php/order/Software/PHP/Version/5.2/Extension/XDebug/Extension/openssl', $urlCfg );
 *
 * $params = $url->getParams(); // will be array( 'Software', 'PHP', 'Version', '5.2', 'Extension', 'XDebug', 'Extension', 'openssl' ) 
 * </code>
 *
 * @property string $host
 *           Hostname or null
 * @property array(string) $path
 *           Complete path as an array.
 * @property string $user
 *           User or null.
 * @property string $pass
 *           Password or null.
 * @property string $port
 *           Port or null.
 * @property string $scheme
 *           Protocol or null.
 * @property array(string=>mixed) $query
 *           Complete query string as an associative array.
 * @property string $fragment
 *           Anchor or null.
 * @property array(string) $basedir
 *           Base directory (the part before the script name) or null.
 * @property array(string) $script
 *           Script name (eg. 'index.php') or null.
 * @property array(string) $params
 *           Complete ordered parameters as array.
 * @property array(string=>mixed) $uparams
 *           Complete unordered parameters as associative array.
 * @property ezcUrlConfiguration $configuration
 *           The URL configuration defined for this URL, or null.
 *
 * @package Url
 * @version 1.2.2
 * @mainclass
 */
class ezcUrl
{
    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Constructs a new ezcUrl object from the string $url.
     *
     * If the $configuration parameter is provided, then it will apply the
     * configuration to the URL by calling {@link applyConfiguration()}.
     *
     * @param string $url A string URL from which to construct the URL object
     * @param ezcUrlConfiguration $configuration An optional URL configuration used when parsing and building the URL
     */
    public function __construct( $url = null, ezcUrlConfiguration $configuration = null )
    {
        $this->parseUrl( $url );
        $this->configuration = $configuration;
        if ( $configuration != null )
        {
            $this->applyConfiguration( $configuration );
        }
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'host':
            case 'path':
            case 'user':
            case 'pass':
            case 'port':
            case 'scheme':
            case 'fragment':
            case 'query':
            case 'basedir':
            case 'script':
            case 'params':
            case 'uparams':
                $this->properties[$name] = $value;
                break;

            case 'configuration':
                if ( $value === null || $value instanceof ezcUrlConfiguration )
                {
                    $this->properties[$name] = $value;
                }
                else
                {
                    throw new ezcBaseValueException( $name, $value, 'instance of ezcUrlConfiguration' );
                }
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
                break;
        }
    }

    /**
     * Returns the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name does not exist
     * @param string $name The name of the property for which to return the value
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'host':
            case 'path':
            case 'user':
            case 'pass':
            case 'port':
            case 'scheme':
            case 'fragment':
            case 'query':
            case 'basedir':
            case 'script':
            case 'params':
            case 'uparams':
            case 'configuration':
                return $this->properties[$name];

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name The name of the property to test if it is set
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        switch ( $name )
        {
            case 'host':
            case 'path':
            case 'user':
            case 'pass':
            case 'port':
            case 'scheme':
            case 'fragment':
            case 'query':
            case 'basedir':
            case 'script':
            case 'params':
            case 'uparams':
            case 'configuration':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Returns this URL as a string by calling {@link buildUrl()}.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->buildUrl();
    }

    /**
     * Parses the string $url and sets the class properties.
     *
     * @param string $url A string URL to parse
     */
    protected function parseUrl( $url = null )
    {
        $urlArray = parse_url( $url );

        $this->properties['host'] = isset( $urlArray['host'] ) ? $urlArray['host'] : null;
        $this->properties['user'] = isset( $urlArray['user'] ) ? $urlArray['user'] : null;
        $this->properties['pass'] = isset( $urlArray['pass'] ) ? $urlArray['pass'] : null;
        $this->properties['port'] = isset( $urlArray['port'] ) ? $urlArray['port'] : null;
        $this->properties['scheme'] = isset( $urlArray['scheme'] ) ? $urlArray['scheme'] : null;
        $this->properties['fragment'] = isset( $urlArray['fragment'] ) ? $urlArray['fragment'] : null;
        $this->properties['path'] = isset( $urlArray['path'] ) ? explode( '/', trim( $urlArray['path'], '/' ) ) : array();

        $this->properties['basedir'] = array();
        $this->properties['script'] = array();
        $this->properties['params'] = array();
        $this->properties['uparams'] = array();

        if ( isset( $urlArray['query'] ) )
        {
            $this->properties['query'] = ezcUrlTools::parseQueryString( $urlArray['query'] );
        }
        else
        {
            $this->properties['query'] = array();
        }
    }

    /**
     * Applies the URL configuration $configuration to the current url.
     *
     * It fills the arrays $basedir, $script, $params and $uparams with values
     * from $path.
     *
     * It also sets the property configuration to the value of $configuration.
     *
     * @param ezcUrlConfiguration $configuration An URL configuration used in parsing
     */
    public function applyConfiguration( ezcUrlConfiguration $configuration )
    {
        $this->configuration = $configuration;
        $this->basedir = $this->parsePathElement( $configuration->basedir, 0 );
        $this->script = $this->parsePathElement( $configuration->script, count( $this->basedir ) );
        $this->params = $this->parseOrderedParameters( $configuration->orderedParameters, count( $this->basedir ) + count( $this->script ) );
        $this->uparams = $this->parseUnorderedParameters( $configuration->unorderedParameters, count( $this->basedir ) + count( $this->script ) + count( $this->params ) );
    }

    /**
     * Parses $path based on the configuration $config, starting from $index.
     *
     * Returns the first few elements of $this->path matching $config,
     * starting from $index.
     *
     * @param string $config A string which will be matched against the path part of the URL
     * @param int $index The index in the URL path part from where to start the matching of $config
     * @return array(string=>mixed)
     */
    private function parsePathElement( $config, $index )
    {
        $config = trim( $config, '/' );
        $paramParts = explode( '/', $config );
        $pathElement = array();
        foreach ( $paramParts as $part )
        {
            if ( isset( $this->path[$index] ) && $part == $this->path[$index] )
            {
                $pathElement[] = $part;
            }
            $index++;
        }
        return $pathElement;
    }

    /**
     * Returns ordered parameters from the $path array.
     *
     * @param array(string) $config An array of ordered parameters names, from the URL configuration used in parsing
     * @param int $index The index in the URL path part from where to start the matching of $config
     * @return array(string=>mixed)
     */
    public function parseOrderedParameters( $config, $index )
    {
        $result = array();
        $pathCount = count( $this->path );
        for ( $i = 0; $i < count( $config ); $i++ )
        {
            if ( isset( $this->path[$index + $i] ) )
            {
                $result[] = $this->path[$index + $i];
            }
            else
            {
                $result[] = null;
            }
        }
        return $result;
    }

    /**
     * Returns unordered parameters from the $path array.
     *
     * The format of the returned array is:
     * <code>
     * array( param_name1 => array( 0 => array( value1, value2, ... ),
     *                              1 => array( value1, value2, ... ) ),
     *        param_name2 = array( 0 => array( value1, value2, ... ),
     *                              1 => array( value1, value2, ... ) ), ... )
     * </code>
     * where 0, 1, etc are numbers meaning the nth encounter of each param_name
     * in the url.
     *
     * For example, if the URL is 'http://www.example.com/(param1)/a/(param2)/x/(param2)/y/z'
     * then the result of this function will be:
     * <code>
     *   array( 'param1' => array( 0 => array( 'a' ) ),
     *          'param2' => array( 0 => array( 'x' ),
     *                             1 => array( 'y', 'z' ) ) );
     * </code>
     *
     * For the URL 'http://www.example.com/(param1)/x/(param1)/y/z', these
     * methods can be employed to get the values of param1:
     * <code>
     * $urlCfg = new ezcUrlConfiguration();
     *
     * // single parameter value
     * $urlCfg->addUnorderedParameter( 'param1' ); // type is SINGLE_ARGUMENT by default
     * $url = new ezcUrl( 'http://www.example.com/(param1)/x/(param1)/y/z', $urlCfg );
     * $param1 = $url->getParam( 'param1' ); // will return "y"
     *
     * // multiple parameter values
     * $urlCfg->addUnorderedParameter( 'param1', ezcUrlConfiguration::MULTIPLE_ARGUMENTS );
     * $url = new ezcUrl( 'http://www.example.com/(param1)/x/(param1)/y/z', $urlCfg );
     * $param1 = $url->getParam( 'param1' ); // will return array( "y", "z" )
     *
     * // multiple parameter values with aggregation
     * $urlCfg->addUnorderedParameter( 'param1', ezcUrlConfiguration::AGGREGATE_ARGUMENTS );
     * $url = new ezcUrl( 'http://www.example.com/(param1)/x/(param1)/y/z', $urlCfg );
     * $param1 = $url->getParam( 'param1' ); // will return array( array( "x" ), array( "y", "z" ) )
     * </code>
     *
     * Note: in the examples above, if the URL does not contain the string 'param1',
     * then all the unordered parameters from and including param1 will be null,
     * so $url->getParam( 'param1' ) will return null (see issue #12825).
     *
     * @param array(string) $config An array of unordered parameters names, from the URL configuration used in parsing
     * @param int $index The index in the URL path part from where to start the matching of $config
     * @return array(string=>mixed)
     */
    public function parseUnorderedParameters( $config, $index )
    {
        $result = array();

        // holds how many times a parameter name is encountered in the URL.
        // for example, for '/(param1)/a/(param2)/x/(param2)/y',
        // $encounters = array( 'param1' => 1, 'param2' => 2 );
        $encounters = array();

        $urlCfg = $this->configuration;
        $pathCount = count( $this->path );
        if ( $pathCount == 0 || ( $pathCount == 1 && trim( $this->path[0] ) === "" ) )
        {
            // special case: a bug? in parse_url() which makes $this->path
            // be array( "" ) if the provided URL is null or empty
            return $result;
        }
        for ( $i = $index; $i < $pathCount; $i++ )
        {
            $param = $this->path[$i];
            if ( strlen( $param ) > 1 &&
                 $param{0} == $urlCfg->unorderedDelimiters[0] )
            {
                $param = trim( trim( $param, $urlCfg->unorderedDelimiters[0] ), $urlCfg->unorderedDelimiters[1] );
                if ( isset( $encounters[$param] ) )
                {
                    $encounters[$param]++;
                }
                else
                {
                    $encounters[$param] = 0;
                }
                $result[$param][$encounters[$param]] = array();
                $j = 1;
                while ( ( $i + $j ) < $pathCount && $this->path[$i + $j]{0} != $urlCfg->unorderedDelimiters[0] )
                {
                    $result[$param][$encounters[$param]][] = trim( trim( $this->path[$i + $j], $urlCfg->unorderedDelimiters[0] ), $urlCfg->unorderedDelimiters[1] );
                    $j++;
                }
            }
        }
        return $result;
    }

    /**
     * Returns this URL as a string.
     *
     * The query part of the URL is build with http_build_query() which
     * encodes the query in a similar way to urlencode().
     *
     * If $includeScriptName is true, then the script name (eg. 'index.php')
     * will be included in the result. By default the script name is hidden (to
     * ensure backwards compatibility).
     *
     * @apichange The default value for $includeScriptName might be changed to
     *            true in future versions
     *
     * @param bool $includeScriptName
     * @return string
     */
    public function buildUrl( $includeScriptName = false )
    {
        $url = '';

        if ( $this->scheme )
        {
            $url .= $this->scheme . '://';
        }

        if ( $this->host )
        {
            if ( $this->user )
            {
                $url .= $this->user;
                if ( $this->pass )
                {
                    $url .= ':' . $this->pass;
                }
                $url .= '@';
            }

            $url .= $this->host;
            if ( $this->port )
            {
                $url .= ':' . $this->port;
            }
        }

        if ( $this->configuration != null )
        {
            if ( $this->basedir )
            {
                if ( !( count( $this->basedir ) == 0 || trim( $this->basedir[0] ) === "" ) )
                {
                    $url .= '/' . implode( '/', $this->basedir );
                }
            }

            if ( $includeScriptName && $this->script )
            {
                if ( !( count( $this->script ) == 0 || trim( $this->script[0] ) === "" ) )
                {
                    $url .= '/' . implode( '/', $this->script );
                }
            }

            if ( $this->params && count( $this->params ) != 0 )
            {
                $url .= '/' . implode( '/', $this->params );
            }

            if ( $this->uparams && count( $this->uparams ) != 0 )
            {
                foreach ( $this->properties['uparams'] as $key => $encounters )
                {
                    foreach ( $encounters as $encounter => $values )
                    {
                        $url .= '/(' . $key . ')/' . implode( '/', $values );
                    }
                }
            }
        }
        else
        {
            if ( $this->path )
            {
                $url .= '/' . implode( '/', $this->path );
            }
        }

        if ( $this->query )
        {
            $url .= '?' . http_build_query( $this->query );
        }

        if ( $this->fragment )
        {
            $url .= '#' . $this->fragment;
        }

        return $url;
    }

    /**
     * Returns true if this URL is relative and false if the URL is absolute.
     *
     * @return bool
     */
    public function isRelative()
    {
        if ( $this->host === null || $this->host == '' )
        {
            return true;
        }
        return false;
    }

    /**
     * Returns the value of the specified parameter from the URL based on the
     * active URL configuration.
     *
     * Unordered parameter examples:
     * <code>
     * $urlCfg = new ezcUrlConfiguration();
     *
     * // single parameter value
     * $urlCfg->addUnorderedParameter( 'param1' ); // type is SINGLE_ARGUMENT by default
     * $url = new ezcUrl( 'http://www.example.com/(param1)/x/(param1)/y/z', $urlCfg );
     * $param1 = $url->getParam( 'param1' ); // will return "y"
     *
     * // multiple parameter values
     * $urlCfg->addUnorderedParameter( 'param1', ezcUrlConfiguration::MULTIPLE_ARGUMENTS );
     * $url = new ezcUrl( 'http://www.example.com/(param1)/x/(param1)/y/z', $urlCfg );
     * $param1 = $url->getParam( 'param1' ); // will return array( "y", "z" )
     *
     * // multiple parameter values with aggregation
     * $urlCfg->addUnorderedParameter( 'param1', ezcUrlConfiguration::AGGREGATE_ARGUMENTS );
     * $url = new ezcUrl( 'http://www.example.com/(param1)/x/(param1)/y/z', $urlCfg );
     * $param1 = $url->getParam( 'param1' ); // will return array( array( "x" ), array( "y", "z" ) )
     * </code>
     *
     * Ordered parameter examples:
     * <code>
     * $urlCfg = new ezcUrlConfiguration();
     *
     * $urlCfg->addOrderedParameter( 'param1' );
     * $urlCfg->addOrderedParameter( 'param2' );
     * $url = new ezcUrl( 'http://www.example.com/x/y', $urlCfg );
     * $param1 = $url->getParam( 'param1' ); // will return "x"
     * $param2 = $url->getParam( 'param2' ); // will return "y"
     * </code>
     *
     * @throws ezcUrlNoConfigurationException
     *         if an URL configuration is not defined
     * @throws ezcUrlInvalidParameterException
     *         if the specified parameter is not defined in the URL configuration
     * @param string $name The name of the parameter for which to return the value
     * @return mixed
     */
    public function getParam( $name )
    {
        $urlCfg = $this->configuration;
        if ( $urlCfg != null )
        {
            if ( !( isset( $urlCfg->orderedParameters[$name] ) ||
                    isset( $urlCfg->unorderedParameters[$name] ) ) )
            {
                throw new ezcUrlInvalidParameterException( $name );
            }

            $params = $this->params;
            $uparams = $this->uparams;
            if ( isset( $urlCfg->orderedParameters[$name] ) &&
                 isset( $params[$urlCfg->orderedParameters[$name]] ) )
            {
                return $params[$urlCfg->orderedParameters[$name]];
            }

            if ( isset( $urlCfg->unorderedParameters[$name] ) &&
                 isset( $uparams[$name][0] ) )
            {
                if ( $urlCfg->unorderedParameters[$name] === ezcUrlConfiguration::SINGLE_ARGUMENT )
                {
                    if ( count( $uparams[$name][0] ) > 0 )
                    {
                        return $uparams[$name][count( $uparams[$name] ) - 1][0];
                    }
                    else
                    {
                        return null;
                    }
                }
                else
                {
                    if ( $urlCfg->unorderedParameters[$name] === ezcUrlConfiguration::AGGREGATE_ARGUMENTS )
                    {
                        $result = $uparams[$name];
                        return $result;
                    }
                    else
                    {
                        return $uparams[$name][count( $uparams[$name] ) - 1];
                    }
                }
            }
            return null;
        }
        else
        {
            throw new ezcUrlNoConfigurationException( $name );
        }
    }

    /**
     * Sets the specified parameter in the URL based on the URL configuration.
     *
     * For ordered parameters, the value cannot be an array, otherwise an
     * ezcBaseValueException will be thrown.
     *
     * For unordered parameters, the value can be one of:
     *  - string
     *  - array(string)
     *  - array(array(string))
     *
     * Any of these values can be assigned to an unordered parameter, whatever the
     * parameter type (SINGLE_ARGUMENT, MULTIPLE_ARGUMENTS, AGGREGATE_ARGUMENTS).
     *
     * If there are ordered and unordered parameters with the same name, only the
     * ordered parameter value will be set.
     *
     * Examples:
     * <code>
     * $urlCfg = new ezcUrlConfiguration();
     * $urlCfg->addUnorderedParameter( 'param1' );
     *
     * $url = new ezcUrl( 'http://www.example.com' );
     *
     * $url->setParam( 'param1', 'x' );
     * echo $url->buildUrl(); // will output http://www.example.com/(param1)/x
     *
     * $url->setParam( 'param1', array( 'x', 'y' ) );
     * echo $url->buildUrl(); // will output http://www.example.com/(param1)/x/y
     *
     * $url->setParam( 'param1', array( array( 'x' ), array( 'y', 'z' ) ) );
     * echo $url->buildUrl(); // will output http://www.example.com/(param1)/x/(param1)/y/z
     * </code>
     *
     * @throws ezcBaseValueException
     *         if trying to assign an array value to an ordered parameter
     * @throws ezcUrlNoConfigurationException
     *         if an URL configuration is not defined
     * @throws ezcUrlInvalidParameterException
     *         if the specified parameter is not defined in the URL configuration
     * @param string $name The name of the parameter to set
     * @param string|array(string=>mixed) $value The new value of the parameter
     */
    public function setParam( $name, $value )
    {
        $urlCfg = $this->configuration;
        if ( $urlCfg != null )
        {
            if ( !( isset( $urlCfg->orderedParameters[$name] ) ||
                    isset( $urlCfg->unorderedParameters[$name] ) ) )
            {
                throw new ezcUrlInvalidParameterException( $name );
            }

            if ( isset( $urlCfg->orderedParameters[$name] ) )
            {
                if ( !is_array( $value ) )
                {
                    $this->properties['params'][$urlCfg->orderedParameters[$name]] = $value;
                }
                else
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }
                return;
            }

            if ( isset( $urlCfg->unorderedParameters[$name] ) )
            {
                if ( !isset( $this->properties['uparams'][$name] ) )
                {
                    $this->properties['uparams'][$name] = array();
                }
                    
                if ( is_array( $value ) )
                {
                    $multiple = false;
                    foreach ( $value as $part )
                    {
                        if ( is_array( $part ) )
                        {
                            $this->properties['uparams'][$name] = $value;
                            $multiple = true;
                            break;
                        }
                    }
                    if ( !$multiple )
                    {
                        $this->properties['uparams'][$name][count( $this->properties['uparams'][$name] ) - 1] = $value;
                    }
                }
                else
                {
                    $this->properties['uparams'][$name][count( $this->properties['uparams'][$name] ) - 1] = array( $value );
                }
            }
            return;
        }
        else
        {
            throw new ezcUrlNoConfigurationException( $name );
        }
    }

    /**
     * Returns the unordered parameters from the URL as a flat array.
     *
     * It takes into account the basedir, script and ordered parameters.
     *
     * It can be used for URLs which don't have delimiters for the unordered
     * parameters.
     *
     * Example:
     * <code>
     * $urlCfg = new ezcUrlConfiguration();
     * $urlCfg->basedir = '/mydir/shop';
     * $urlCfg->script = 'index.php';
     * $urlCfg->addOrderedParameter( 'module' );
     *
     * $url = new ezcUrl( 'http://www.example.com/mydir/shop/index.php/order/Software/PHP/Version/5.2/Extension/XDebug/Extension/openssl', $urlCfg );
     *
     * $params = $url->getParams(); // will be array( 'Software', 'PHP', 'Version', '5.2', 'Extension', 'XDebug', 'Extension', 'openssl' )
     * </code>
     *
     * @return array(string)
     */
    public function getParams()
    {
        return array_slice( $this->path, count( $this->basedir ) + count( $this->script ) + count( $this->params ) );
    }

    /**
     * Returns the query elements as an associative array.
     *
     * Example:
     * for 'http://www.example.com/mydir/shop?content=view&products=10'
     * returns array( 'content' => 'view', 'products' => '10' )
     *
     * @return array(string=>mixed)
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the query elements using the associative array provided.
     *
     * Example:
     * for 'http://www.example.com/mydir/shop'
     * and $query = array( 'content' => 'view', 'products' => '10' )
     * then 'http://www.example.com/mydir/shop?content=view&products=10'
     *
     * @param array(string=>mixed) $query The new value of the query part
     */
    public function setQuery( $query )
    {
        $this->query = $query;
    }
}
?>
