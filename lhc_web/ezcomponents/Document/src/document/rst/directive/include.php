<?php
/**
 * File containing the ezcDocumentRstIncludeDirective class
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Visitor for RST include directives
 *
 * @package Document
 * @version 1.3.1
 */
class ezcDocumentRstIncludeDirective extends ezcDocumentRstDirective implements ezcDocumentRstXhtmlDirective
{
    /**
     * Check and return file
     *
     * Check for the files location, and return the absolute path to the file,
     * or thorw an exception, if the file could not be found.
     *
     * @param string $file
     * @return string
     */
    protected function getFile( $file )
    {
        if ( !ezcBaseFile::isAbsolutePath( $file ) )
        {
            // If path to file is not an absolute path, use the given relative
            // path relative to the currently processed document location.
            $file = $this->path . $file;
        }

        // @todo: docutils performs automatic checks, that no system files
        // (like /etc/passwd) are included - do we want to do similar stuff
        // here?

        // Throw an exception, if we cannot find the referenced file
        if ( !is_file( $file ) || !is_readable( $file ) )
        {
            throw new ezcBaseFileNotFoundException( $file );
        }

        return $file;
    }

    /**
     * Transform directive to docbook
     *
     * Create a docbook XML structure at the directives position in the
     * document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    public function toDocbook( DOMDocument $document, DOMElement $root )
    {
        $file = $this->getFile( trim( $this->node->parameters ) );

        if ( $root->tagName === 'para' )
        {
            // The include is inline. There is no way to handle the embedding
            // of another document inside a paragraph properly.
            throw new ezcDocumentVisitException( E_PARSE, 'Caanot embed include inside a paragraph.' );
        }

        if ( isset( $this->node->options['literal'] ) )
        {
            // If the file should be included as a literal, just pass it
            // through in such a block.
            $literal = $document->createElement( 'literallayout', htmlspecialchars( file_get_contents( $file ) ) );
            $root->appendChild( $literal );
        }
        else
        {
            // Otherwise we reenter the complete parsing process with the new file.
            $doc = new ezcDocumentRst();
            $doc->loadFile( $file );

            // Get docbook DOM tree from the parsed file
            $docbook = $doc->getAsDocbook();
            $dom = $docbook->getDomDocument();

            // Import and add the complete parsed document.
            $article = $dom->getElementsByTagName( 'article' )->item( 0 );
            foreach ( $article->childNodes as $child )
            {
                $imported = $document->importNode( $child, true );
                $root->appendChild( $imported );
            }
        }
    }

    /**
     * Transform directive to HTML
     *
     * Create a XHTML structure at the directives position in the document.
     *
     * @param DOMDocument $document
     * @param DOMElement $root
     * @return void
     */
    public function toXhtml( DOMDocument $document, DOMElement $root )
    {
        $file = $this->getFile( trim( $this->node->parameters ) );

        if ( $root->tagName === 'p' )
        {
            // The include is inline. There is no way to handle the embedding
            // of another document inside a paragraph properly.
            throw new ezcDocumentVisitException( E_PARSE, 'Caanot embed include inside a paragraph.' );
        }

        if ( isset( $this->node->options['literal'] ) )
        {
            // If the file should be included as a literal, just pass it
            // through in such a block.
            $literal = $document->createElement( 'pre', htmlspecialchars( file_get_contents( $file ) ) );
            $root->appendChild( $literal );
        }
        else
        {
            // Otherwise we reenter the complete parsing process with the new file.
            $doc = new ezcDocumentRst();
            $doc->loadFile( $file );

            // Get XHtml DOM tree from the parsed file
            $html = $doc->getAsXhtml();
            $tree = $html->getDomDocument();

            // Import all contents below the body node.
            $article = $tree->getElementsByTagName( 'body' )->item( 0 );
            foreach ( $article->childNodes as $child )
            {
                $imported = $document->importNode( $child, true );
                $root->appendChild( $imported );
            }
        }
    }
}

?>
