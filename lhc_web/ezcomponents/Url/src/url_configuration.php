<?php
/**
 * File containing the ezcUrlConfiguration class.
 *
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.2.2
 * @filesource
 * @package Url
 */

/**
 * ezcUrlConfiguration makes it possible to use a custom URL form in your application.
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
 *
 * // to remove parameters from the URL configuration $urlCfg
 * $urlCfg->removeOrderedParameter( 'subcategory' );
 * $urlCfg->removeUnorderedParameter( 'game' );
 *
 * // to remove parameters from the URL configuration stored in the URL
 * $url->configuration->removeOrderedParameter( 'subcategory' );
 * $url->configuration->removeUnorderedParameter( 'game' );
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
 * @property string $basedir
 *           The part of the URL after the first slash. It can be null.
 *           Example: $basedir = shop in http://www.example.com/shop
 * @property string $script
 *           The default php script, which comes after the basedir. Can be null
 *           if the web server configuration is set to hide it.
 *           Example: $script = index.php in http://www.example.com/shop/index.php
 * @property array(string) $unorderedDelimiters
 *           The delimiters for the unordered parameters names.
 *           Example: $unorderedDelimiters = array( '(', ')' ) for
 *              url = http://www.example.com/doc/(file)/classtrees_Base.html
 * @property array(string=>int) $orderedParameters
 *           The ordered parameters of the URL.
 *           Example: $orderedParameters = array( 'section' => 0, 'module' => 1, 'view' => 2, 'content' => 3 );
 *              url = http://www.example.com/doc/components/view/trunk
 *           The numbers in the array represent the indices for each parameter.
 * @property array(string=>int) $unorderedParameters
 *           The unordered parameters of the URL.
 *           Example: $unorderedParameters = array( 'file' => SINGLE_ARGUMENT );
 *              url = http://www.example.com/doc/(file)/classtrees_Base.html
 *           The keys of the array represent the parameter names, and the values
 *           in the array represent the types of the parameters.
 *
 * @package Url
 * @version 1.2.2
 */
class ezcUrlConfiguration
{
    /**
     * Flag for specifying single arguments for unordered parameters.
     */
    const SINGLE_ARGUMENT = 1;

    /**
     * Flag for specifying multiple arguments for unordered parameters.
     */
    const MULTIPLE_ARGUMENTS = 2;

    /**
     * Flag for specifying aggregation for unordered parameter values if the
     * parameter name appears more than once in the URL.
     *
     * For example, if the URL is 'http://www.example.com/(param1)/x/(param1)/y/z',
     * then all values will be considered for the parameter param1. So
     * $url->getParam( 'param1' ) will return array( array( "x" ), array( "y", "z" ) ),
     * if $url is an ezcUrl object created from the above URL.
     */
    const AGGREGATE_ARGUMENTS = 4;

    /**
     * Holds the properties of this class.
     *
     * @var array(string=>mixed)
     */
    private $properties = array();

    /**
     * Stores the instance of this class.
     *
     * @var ezcUrlConfiguration
     */
    private static $instance = null;

    /**
     * Constructs a new ezcUrlConfiguration object.
     *
     * The properties of the object get default values, which can be changed by
     * setting the properties directly, like:
     * <code>
     *   $urlCfg = new ezcUrlConfiguration();
     *   $urlCfg->basedir = 'mydir';
     *   $urlCfg->script = 'index.php';
     * </code>
     */
    public function __construct()
    {
        $this->basedir = null;
        $this->script = null;
        $this->unorderedDelimiters = array( '(', ')' );
        $this->orderedParameters = array();
        $this->unorderedParameters = array();
    }

    /**
     * Returns the instance of the class.
     *
     * @return ezcUrlConfiguration
     */
    public static function getInstance()
    {
        if ( is_null( self::$instance ) )
        {
            self::$instance = new ezcUrlConfiguration();
            ezcBaseInit::fetchConfig( 'ezcUrlConfiguration', self::$instance );
        }
        return self::$instance;
    }

    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist.
     * @param string $name The name of the property to set
     * @param mixed $value The new value of the property
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'basedir':
            case 'script':
            case 'unorderedDelimiters':
            case 'orderedParameters':
            case 'unorderedParameters':
                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }

    /**
     * Returns the property $name.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property does not exist.
     * @param string $name The name of the property for which to return the value
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        switch ( $name )
        {
            case 'basedir':
            case 'script':
            case 'unorderedDelimiters':
            case 'orderedParameters':
            case 'unorderedParameters':
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
            case 'basedir':
            case 'script':
            case 'unorderedDelimiters':
            case 'orderedParameters':
            case 'unorderedParameters':
                return isset( $this->properties[$name] );

            default:
                return false;
        }
    }

    /**
     * Adds an ordered parameter to the URL configuration.
     *
     * @param string $name The name of the ordered parameter to add to the configuration
     */
    public function addOrderedParameter( $name )
    {
        $this->properties['orderedParameters'][$name] = count( $this->properties['orderedParameters'] );
    }

    /**
     * Removes an ordered parameter from the URL configuration.
     *
     * @param string $name The name of the ordered parameter to remove from the configuration
     */
    public function removeOrderedParameter( $name )
    {
        if ( isset( $this->properties['orderedParameters'][$name] ) )
        {
            unset( $this->properties['orderedParameters'][$name] );
        }
    }

    /**
     * Adds an unordered parameter to the URL configuration.
     *
     * The possible values for the $type parameter are:
     *  - {@link ezcUrlConfiguration::SINGLE_ARGUMENT} (default): the getParam()
     *    method in ezcUrl will return a string containing the value of the
     *    parameter $name
     *  - {@link ezcUrlConfiguration::MULTIPLE_ARGUMENTS}: the getParam() method
     *    will return an array containing the last encountered values of the
     *    parameter $name
     *  - {@link ezcUrlConfiguration::AGGREGATE_ARGUMENTS}: the getParam() method
     *    will return an array with all encountered values for the parameter $name
     *
     * Examples:
     * <code>
     * $urlCfg = new ezcUrlConfiguration();
     *
     * // single parameter value
     * $urlCfg->addUnorderedParameter( 'param1' ); // type is SINGLE_ARGUMENT by default
     * $url = new ezcUrl( 'http://www.example.com/(param1)/x', $urlCfg );
     * $param1 = $url->getParam( 'param1' ); // will return "x"
     *
     * // multiple parameter values
     * $urlCfg->addUnorderedParameter( 'param1', ezcUrlConfiguration::MULTIPLE_ARGUMENTS );
     * $url = new ezcUrl( 'http://www.example.com/(param1)/x/y', $urlCfg );
     * $param1 = $url->getParam( 'param1' ); // will return array( "x", "y" )
     *
     * // multiple parameter values with aggregation
     * $urlCfg->addUnorderedParameter( 'param1', ezcUrlConfiguration::AGGREGATE_ARGUMENTS );
     * $url = new ezcUrl( 'http://www.example.com/(param1)/x/(param1)/y/z', $urlCfg );
     * $param1 = $url->getParam( 'param1' ); // will return array( array( "x" ), array( "y", "z" ) )
     * </code>
     *
     * @param string $name The name of the unordered parameter to add to the configuration
     * @param int $type The type of the unordered parameter
     */
    public function addUnorderedParameter( $name, $type = null )
    {
        if ( $type == null )
        {
            $type = self::SINGLE_ARGUMENT;
        }
        $this->properties['unorderedParameters'][$name] = $type;
    }

    /**
     * Removes an unordered parameter from the URL configuration.
     *
     * @param string $name The name of the unordered parameter to remove from the configuration
     */
    public function removeUnorderedParameter( $name )
    {
        if ( isset( $this->properties['unorderedParameters'][$name] ) )
        {
            unset( $this->properties['unorderedParameters'][$name] );
        }
    }
}
?>
