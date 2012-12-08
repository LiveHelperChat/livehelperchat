<?php
/**
 * File containing the ezcMvcJsonViewHandler class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * The view handler that renders result objects as JSON.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
class ezcMvcJsonViewHandler implements ezcMvcViewHandler
{
    /**
     * Contains the zone name
     *
     * @var string
     */
    protected $zoneName;

    /**
     * Contains the result after process() has been called.
     *
     * @var mixed
     */
    protected $result;

    /**
     * Contains the variables that will be available in the template.
     *
     * @var array(mixed)
     */
    protected $variables = array();

    /**
     * Creates a new view handler, where $zoneName is the name of the block and
     * $templateLocation the location of a view template.
     *
     * @param string $zoneName
     * @param string $templateLocation
     */
    public function __construct( $zoneName, $templateLocation = null )
    {
        $this->zoneName = $zoneName;
    }

    /**
     * Adds a variable to the template, which can then be used for rendering
     * the view.
     *
     * @param string $name
     * @param mixed $value
     */
    public function send( $name, $value )
    {
        $this->variables[$name] = $value;
    }

    /**
     * Processes the template with the variables added by the send() method.
     * The result of this action should be retrievable through the getResult() method.
     *
     * The $last parameter is set if the view handler is the last one in the
     * list of zones for a specific view. Only in that case is json_encode()
     * used to generate JSON output.
     *
     * @param bool $last
     */
    public function process( $last )
    {
        $this->result = $this->variables;
        if ( $last )
        {
            $this->result = json_encode( $this->result );
        }
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @ignore
     */
    public function __get( $name )
    {
        return $this->variables[$name];
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        return array_key_exists( $name, $this->variables );
    }

    /**
     * Returns the name of the template, as set in the constructor.
     *
     * @return string
     */
    public function getName()
    {
        return $this->zoneName;
    }

    /**
     * Returns the result of the process() method.
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}
?>
