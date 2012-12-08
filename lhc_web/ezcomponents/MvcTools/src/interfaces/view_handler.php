<?php
/**
 * File containing the ezcMvcViewHandler class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Interface defining view handlers.
 *
 * A view handler is the implementation of a view that converts the abstract
 * ezcMvcResult objects to ezcMvcResponse objects - which are then send to the
 * client with a response writer.
 *
 * @package MvcTools
 * @version 1.1.3
 */
interface ezcMvcViewHandler
{
    /**
     * Creates a new view handler, where $name is the name of the block and
     * $templateLocation the location of a view template.
     *
     * @param string $name
     * @param string $templateLocation
     */
    public function __construct( $name, $templateLocation = null );

    /**
     * Adds a variable to the template, which can then be used for rendering
     * the view.
     *
     * @param string $name
     * @param mixed $value
     */
    public function send( $name, $value );

    /**
     * Processes the template with the variables added by the send() method.
     * The result of this action should be retrievable through the getResult() method.
     *
     * The $last parameter is set if the view handler is the last one in the
     * list of zones for a specific view.
     *
     * @param bool $last
     */
    public function process( $last );

    /**
     * Returns the name of the template, as set in the constructor.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the result of the process() method.
     *
     * @return mixed
     */
    public function getResult();
}
?>
