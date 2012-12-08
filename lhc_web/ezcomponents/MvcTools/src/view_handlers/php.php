<?php
/**
 * File containing the ezcMvcPhpViewHandler class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * The view handler that uses PHP files to render result objects.
 *
 * @package MvcTools
 * @version 1.1.3
 * @mainclass
 */
class ezcMvcPhpViewHandler implements ezcMvcViewHandler
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
     * Contains the path to the template file.
     *
     * @var string
     */
    protected $templateLocation;

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
        $this->templateLocation = $templateLocation;
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
     * list of zones for a specific view.
     *
     * @param bool $last
     */
    public function process( $last )
    {
        if ( !file_exists( $this->templateLocation ) )
        {
            $fileName = ezcBaseFile::isAbsolutePath( $this->templateLocation ) ? $this->templateLocation : getcwd() . DIRECTORY_SEPARATOR . $this->templateLocation;
            throw new ezcBaseFileNotFoundException( $fileName, 'php template' );
        }
        ob_start();
        include $this->templateLocation;
        $this->result = ob_get_contents();
        ob_end_clean();
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
