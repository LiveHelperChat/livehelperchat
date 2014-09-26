<?php
/**
 * File containing the XMLWriter class.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class implements a quick and dirty fallback in the case the PHP extension XMLWriter is not available.
 *
 * @package DatabaseSchema
 * @version 1.4.4
 * @access private
 */
class XMLWriter
{
    private $elementStack;
    private $uriFs = false;
    
    public function __construct()
    {
        $this->elementStack = array();
    }

    public function openUri( $filename )
    {
        $this->uriFs = fopen( $filename, 'w' );
        return $this->uriFs;
    }

    public function startDocument( $version, $charset = 'utf-8' )
    {
        fputs( $this->uriFs, "<?xml version='$version' encoding='$charset' ?>\n" );
    }

    public function startElement( $name )
    {
        fputs( $this->uriFs, "<$name>" );
        array_push( $this->elementStack, $name );
    }

    public function endElement()
    {
        $name = array_pop( $this->elementStack );
        fputs( $this->uriFs, "</$name>" );
    }

    public function text( $text )
    {
        fputs( $this->uriFs, $text );
    }

    public function endDocument()
    {
        fclose( $this->uriFs );
    }

    public function flush()
    {
        fputs( $this->uriFs, "\n" );
    }

    public function setIndent( $switch )
    {
    }
}
?>
