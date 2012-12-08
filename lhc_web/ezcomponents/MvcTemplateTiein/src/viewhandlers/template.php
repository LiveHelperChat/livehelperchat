<?php
/**
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.0
 * @filesource
 * @package MvcTemplateTiein
 */

/**
 * The view handler that uses the template component to render result objects.
 * 
 * @package MvcTemplateTiein
 * @version 1.0
 * @mainclass
 */
class ezcMvcTemplateViewHandler implements ezcMvcViewHandler
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
        $this->templateLocation = $templateLocation;

        $this->template = new ezcTemplate;
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
        $this->template->send->$name = $value;
    }

    /**
     * Processes the template with the variables added by the send() method.
     * The result of this action should be retrievable through the getResult() method.
     *
     * @param mixed $last
     */
    public function process( $last )
    {
        $this->result = $this->template->process( $this->templateLocation );
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
