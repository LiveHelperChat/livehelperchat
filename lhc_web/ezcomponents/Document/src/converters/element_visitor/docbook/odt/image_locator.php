<?php
/**
 * File containing the ezcDocumentOdtImageLocator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Class to locate images in DocBook to ODT conversion.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentOdtImageLocator
{
    /**
     * Paths to search for images.
     * 
     * @var array(string)
     */
    protected $paths = array();

    /**
     * Creates a new image locator for the given $document.
     * 
     * @param ezcDocument $document 
     */
    public function __construct( ezcDocument $document )
    {
        $this->paths[] = $document->getPath();

        if ( ( $workDir = getcwd() ) !== false )
        {
            $this->paths[] = $workDir;
        }

        $this->paths[] = sys_get_temp_dir();
    }

    /**
     * Returns the realpath of the given image $fileName.
     *
     * Tries to find the image for the given $fileName in the file system and 
     * returns its realpath, if found. If the image cannot be located, false is 
     * returned.
     * 
     * @param string $fileName 
     * @return string|false
     */
    public function locateImage( $fileName )
    {
        if ( file_exists( $fileName ) )
        {
            return realpath( $fileName );
        }

        if ( substr( $fileName, 0, 1 ) === DIRECTORY_SEPARATOR )
        {
            // File name is absolute, but image does not exist
            return false;
        }

        foreach ( $this->paths as $path )
        {
            if ( file_exists( ( $imgPath = $path . DIRECTORY_SEPARATOR . $fileName ) ) )
            {
                return realpath( $imgPath );
            }
        }
        return false;
    }
}

?>
