<?php
/**
 * File contains the ezcArchivePaxHeader class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcArchivePaxHeader class represents the Tar Pax header.
 *
 * ezcArchivePaxHeader can read the header from an ezcArchiveBlockFile or ezcArchiveEntry.
 *
 * The values from the headers are directly accessible via the class properties, and allows
 * reading and writing to specific header values.
 *
 * The entire header can be appended to an ezcArchiveBlockFile again or written to an ezcArchiveFileStructure.
 * Information may get lost, though.
 *
 * The header is the {@link ezcArchiveUstarHeader} with extra header information described on the following webpage:
 * {@link http://www.opengroup.org/onlinepubs/009695399/utilities/pax.html}
 *
 * Currently, only the extended header is supported.
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
class ezcArchivePaxHeader extends ezcArchiveUstarHeader
{
    /**
     * Sets the property $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @return void
     * @ignore
     */
    public function __set( $name, $value )
    {
        return parent::__set( $name, $value );
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        return parent::__get( $name );
    }

    /**
     * Returns an array with pax header information.
     *
     * This method reads an extended set of data from the ezcArchiveBlockFile
     * $file and returns the values in an array.
     *
     * @param ezcArchiveBlockFile $file
     * @return array(string=>string)
     */
    protected function getPaxDecodedHeader( ezcArchiveBlockFile $file )
    {
        $result = array();

        // next block has the info.
        $file->next();

        $data = $file->current();

        $offset = 0;

        while ( strcmp( $data[$offset], "\0" ) != 0 )
        {
            $space = strpos( $data, " ", $offset );

            $length = substr( $data, $offset, $space - $offset );
            $equalSign = strpos( $data, "=",  $space );

            $keyword = substr( $data, $space + 1, $equalSign - $space - 1 );

            $value = rtrim( substr( $data, $equalSign + 1, $length - $equalSign - 1 ), "\n" );

            $result[ $keyword ] = $value;

            $offset += $length;
        }

        return $result;
    }

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
            parent::__construct( $file );

            if ( $this->type == "x" )
            {
                $paxArray = $this->getPaxDecodedHeader( $file );
                $file->next();
            }

            parent::__construct( $file );

            // Override some fields.
            foreach ( $paxArray as $key => $value )
            {
                switch ( $key )
                {
                    case "gid":
                        $this->groupId = $value;
                        break;  // For group IDs larger than 2097151.

                    case "linkpath":
                        $this->linkName = $value;
                        break;  // Long link names?

                    case "path":
                        $this->fileName = $value;
                        $this->filePrefix = "";
                        break; // Really long file names.

                    case "size":
                        $this->size = $value;
                        break;  // For files with a size greater than 8589934591 bytes.

                    case "uid":
                        $this->userId = $value;
                        break;  // For user IDs larger than 2097151.
                }
            }
        }
    }
}
?>
