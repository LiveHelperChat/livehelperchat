<?php
/**
 * File contains the ezcArchiveGnuHeader class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcArchiveGnuHeader class represents the Tar Gnu header.
 *
 * ezcArchiveGnuHeader can read the header from an ezcArchiveBlockFile or ezcArchiveEntry.
 *
 * The values from the headers are directly accessible via the class properties, and allows
 * reading and writing to specific header values.
 *
 * The entire header can be appended to an ezcArchiveBlockFile again or written to an ezcArchiveFileStructure.
 * Information may get lost, though.
 *
 * The header is the {@link ezcArchiveUstarHeader} with the extension that the type can be set to "L" or "K".
 * Respectively, long filename and long link. If the type is set to either "L" or "K", the next block contains the
 * filename or link. The block thereafter repeats the original header but the type is then set to a digit.
 * (No long filename or link).
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
class ezcArchiveGnuHeader extends ezcArchiveUstarHeader
{
    /**
     * Creates and initializes a new header.
     *
     * If the ezcArchiveBlockFile $file is null then the header will be empty.
     * When an ezcArchiveBlockFile is given, the block position should point to the header block.
     * This header block will be read from the file and initialized in this class.
     *
     * @param ezcArchiveBlockFile $file
     */
    public function __construct( ezcArchiveBlockFile $file = null )
    {
        if ( !is_null( $file ) )
        {
            // FIXME  Assumed a while.. check the gnu tar source file.
            // FIXME  Check long links, really large files, etc.
            $extensions = array();

            do
            {
                parent::__construct( $file );

                switch ( $this->type )
                {
                    case "L":
                        $extensions["fileName"] = $this->readExtension( $file );
                        break;

                    case "K":
                        $extensions["linkName"] = $this->readExtension( $file );
                        break;

                }
            } while ( $this->type < '0' || $this->type > '9' );

            parent::__construct( $file );

            foreach ( $extensions as $key => $value )
            {
                switch ( $key )
                {
                    case "fileName":
                        $this->fileName = $extensions["fileName"];
                        $this->filePrefix = "";
                        break;

                    case "linkName":
                        $this->linkName = $extensions["linkName"];
                        break;
                }
            }
        }
    }

    /**
     * Reads an extended set of data from the Block file and returns it as a string.
     *
     * Some filenames or link names do not fit in the Ustar header, and are therefor placed in a new block.
     * This method read the block(s) and returns the data as a string.
     *
     * @param ezcArchiveBlockFile $file
     * @return string
     */
    protected function readExtension( ezcArchiveBlockFile $file )
    {
        $completeBlocks = ( int ) ( $this->fileSize / self::BLOCK_SIZE );
        $rest = ( $this->fileSize % self::BLOCK_SIZE );

        $data  = "";
        for ( $i = 0; $i < $completeBlocks; $i++ )
        {
            $data .=  $file->next();
        }

        $data .= substr( $file->next(), 0, $rest );

        $file->next();
        return $data;
    }
}
?>
