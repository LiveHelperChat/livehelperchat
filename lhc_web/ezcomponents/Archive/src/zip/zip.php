<?php
/**
 * File containing the ezcArchiveZip class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcArchiveZip class implements the Zip archive format.
 *
 * ezcArchiveZip is a subclass from {@link ezcArchive} that provides the common interface.
 * Zip algorithm specific methods are implemented in this class.
 *
 * ezcArchiveZip tries on creation to read the entire archive. Every {@link ezcArchiveEntry}
 * will be appended to the {@link ezcArchive::$entries} array. ({@link ezcArchiveTarV7} archive format
 * reads only an entry when needed the first time.)
 *
 * All the archive entries are read, because we need to find the {@link ezcArchiveCentralDirectoryHeader central directory}
 * that contains extra file information. Some other Zip implementations search back from the end of the archive, but:
 * - Not all of them take the comment section at the end of the archive into account.
 * - The file pointer can only forward, so they seek over the entire archive anyway.
 *
 * The current implementation can handle Non-Zip64 archives. The following extra Zip information can be read:
 * - The Unix Extra field.
 * - The Unix Extra field 2.
 * - The Universal Timestamp field.
 *
 * @package Archive
 * @version 1.4.1
 */
class ezcArchiveZip extends ezcArchive implements Iterator
{
    /**
     * Stores the byte number where the local file header starts for each entry.
     *
     * @var array(int)
     */
    protected $localHeaderPositions;

    /**
     * Stores the {@link ezcArchiveLocalFileHeader} for each entry.
     *
     * @var array(ezcArchiveLocalFileHeader)
     */
    protected $localHeaders;

    /**
     * Stores the byte number where the central directory header starts
     * (fileNumber is the index of the array).
     *
     * @var array(int)
     */
    protected $centralHeaderPositions;

    /**
     * Stores the {@link ezcArchiveCentralDirectoryHeader} for each entry.
     *
     * @var array(ezcArchiveCentralDirectoryHeader)
     */
    protected $centralHeaders;

    /**
     * Stores the {@link ezcArchiveCentralDirectoryEndHeader}.
     *
     * @var ezcArchiveCentralDirectoryEndHeader
     */
    protected $endRecord;

    /**
     * Initializes the Zip archive and reads the entire archive.
     *
     * The constructor opens the archive as a {@link ezcArchiveCharacterFile character file}.
     *
     * @param ezcArchiveCharacterFile $file
     */
    public function __construct( ezcArchiveCharacterFile $file )
    {
        $this->localHeaderPositions = array();
        $this->file = $file;

        $this->fileNumber = 0;
        $this->entriesRead = 0;

        if ( !$this->file->isEmpty() )
        {
            $this->readCentralHeaders();
        }

        $this->completed = true;
    }

    /**
     * Returns the value which specifies a ZIP algorithm.
     *
     * @return int
     */
    public function getAlgorithm()
    {
        return self::ZIP;
    }

    /**
     * Returns true because the ZIP algorithm can write.
     *
     * @see isWritable()
     *
     * @return bool
     */
    public function algorithmCanWrite()
    {
        return true;
    }

    /**
     * Reads the entire archive and creates all the entries.
     *
     * To find the central directory structure we need to read all the headers.
     * Some algorithms search backwards, but these don't expect comments at the end
     * of the archive.
     *
     * The central directory structure gives us extra information about the
     * stored file like: symlinks and permissions.
     *
     * @throws ezcArchiveException
     *         if the comment length is different than 0
     *         or if the signature from the file is invalid
     *         or if the directory central header or size are missing
     * @return void
     */
    protected function readCentralHeaders()
    {
        $this->localHeaders = array();
        $this->centralHeaders = array();

        // read the central end headers

        $this->file->seek( -22, SEEK_END );
        $filesize = $this->file->key() + 22;
        $endRecordPosition = $filesize - 22;

        $sig = $this->file->read( 4 );
        if ( ezcArchiveCentralDirectoryEndHeader::isSignature( $sig ) )
        {
            $this->endRecord = new ezcArchiveCentralDirectoryEndHeader( $this->file );

            if ( $this->endRecord->commentLength != 0 )
            {
                throw new ezcArchiveException( "Comment length invalid." );
            }
        }
        else
        {
            $signatureString = "PK\005\006";

            $startPosition = max( $filesize - ( 5 * 1024 ), 0 );

            // Maybe there is a comment at the end of the archive.
            $this->file->seek( $startPosition );
            $data = $this->file->read( $filesize - $startPosition );

            $pos = strpos( $data, $signatureString );

            if ( $pos === false )
            {
                throw new ezcArchiveException( "Could not find the central-directory header" );
            }

            $endRecordPosition = $startPosition + $pos;

            $this->file->seek( $endRecordPosition );
            $sig = $this->file->read( 4 );

            if ( ezcArchiveCentralDirectoryEndHeader::isSignature( $sig ) )
            {
                $this->endRecord = new ezcArchiveCentralDirectoryEndHeader( $this->file );
            }
            else
            {
                throw new ezcArchiveException( "Zip file corrupt" );
            }

            if ( $this->endRecord->commentLength !=  $filesize - $endRecordPosition - 22 )
            {
                throw new ezcArchiveException( "Comment length invalid." );
            }
        }

        if ( $endRecordPosition - $this->endRecord->centralDirectorySize !== $this->endRecord->centralDirectoryStart )
        {
            throw new ezcArchiveException( "Unable to determine the central directory start." );
        }

        $this->file->seek( $endRecordPosition - $this->endRecord->centralDirectorySize );
        $sig = $this->file->read( 4 );

        $i = 0;
        while ( ezcArchiveCentralDirectoryHeader::isSignature( $sig ) )
        {
            $this->centralHeaderPositions[$i] = $this->file->key() - 4;
            $this->centralHeaders[$i] = new ezcArchiveCentralDirectoryHeader( $this->file );

            $sig = $this->file->read( 4 );

            $this->localHeaderPositions[$i] = $this->centralHeaders[$i]->relativeHeaderOffset;

            $i++;
        }

        // Create the entries and check for symlinks.
        $this->entriesRead = $i;

        for ( $i = 0; $i < $this->entriesRead; $i++ )
        {
            $struct = new ezcArchiveFileStructure();

            // $this->localHeaders[$i]->setArchiveFileStructure( $struct );
            $this->centralHeaders[$i]->setArchiveFileStructure( $struct );


            // Set the symbolic links, 'cause these are written in the file data.
            if ( $this->centralHeaders[$i]->getType() == ezcArchiveEntry::IS_SYMBOLIC_LINK )
            {
                $struct->link = $this->getFileData( $i );
            }
            else
            {
                $struct->link = "";
            }

            $this->entries[$i] = new ezcArchiveEntry( $struct );
        }
    }


    /**
     * Writes the file data from the current entry to the given file.
     *
     * @param string $targetPath  The absolute or relative path of the target file.
     * @return bool
     */
    protected function writeCurrentDataToFile( $targetPath )
    {
        if ( !$this->valid() )
        {
            return false;
        }

        $this->writeFile( $this->key(), $targetPath );

        return true;
    }

    /**
     * Returns the file data of the given fileNumber.
     *
     * This method doesn't handle compression and reads the whole file in memory.
     * This method is used to get the symbolic links, since these are stored in files.
     *
     * For larger or compressed files, use the {@link writeFile()} method.
     *
     * @param int $fileNumber
     * @return string
     */
    public function getFileData( $fileNumber )
    {
        $pos = $this->localHeaderPositions[ $fileNumber ];
        $header = $this->getLocalHeader( $fileNumber );

        $newPos = $pos + $header->getHeaderSize();
        $this->file->seek( $newPos );

        // Read all the data.
        return $this->file->read( $header->compressedSize );
    }

    /**
     * Returns the local header of the specified file number.
     *
     * @throws ezcArchiveException
     *         if the signature from the file is invalid
     * @param int $fileNumber
     */
    public function getLocalHeader( $fileNumber )
    {
        if ( !isset( $this->localHeaders[$fileNumber] ) )
        {
            // Read the local header
            $this->file->seek( $this->localHeaderPositions[$fileNumber] );

            $sig = $this->file->read( 4 );

            if ( !ezcArchiveLocalFileHeader::isSignature( $sig ) )
            {
                throw new ezcArchiveException( "Zip file corrupt?" );
            }

            $this->localHeaders[$fileNumber] = new ezcArchiveLocalFileHeader( $this->file, $this->centralHeaders[$fileNumber] );
        }

        return $this->localHeaders[$fileNumber];
    }

    /**
     * Reads the file data from the archive and writes it the the $writeTo file.
     *
     * The data from the entry with $fileNumber is read from the archive.
     * If the data is compressed or deflated it will be, respectively, decompressed or inflated.
     *
     * @throws ezcArchiveChecksumException if the checksum is invalid from the created file.
     *
     * @param   int     $fileNumber
     * @param   string  $writeTo
     * @return  void
     */
    public function writeFile( $fileNumber,  $writeTo )
    {
        $header = $this->getLocalHeader( $fileNumber );
        $pos = $this->localHeaderPositions[ $fileNumber ] + $header->getHeaderSize();

        // FIXME.. don't write the entire stuff to memory.
        $this->file->seek( $pos );

        /*
        Part: 1
        - Append the stream filter.
        switch ( $header->compressionMethod )
        {
            case 8:  $this->file->appendStreamFilter( "zlib.inflate" ); break;
            case 12: $this->file->appendStreamFilter( "bzip2.decompress" ); break;
        }
        */

        $data = $this->file->read( $header->compressedSize );

        /*
        Part: 2
        - And remove the stream filter.
        - Then we can write the file directly from the archive without copying it entirely to memory.
        - Unfortunately, this method segfaults for me.

        if ( $header->compressionMethod == 8  || $header->compressionMethod == 12 )
        {
            $this->file->removeStreamFilter();
        }
        */

        if ( $data )
        {
            switch ( $header->compressionMethod )
            {
                case 8:
                    $data = gzinflate( $data );
                    break;    // Evil, memory consuming.

                case 12:
                    $data = bzdecompress( $data );
                    break;
            }
        }

        if ( strcmp( sprintf( "%u", crc32( $data ) ), sprintf( "%u", $header->crc & 0xffffffff ) ) == 0 )
        {
            $newFile = new ezcArchiveCharacterFile( $writeTo, true );
            $newFile->write( $data );
            unset( $newFile );
        }
        else
        {
            throw new ezcArchiveChecksumException( $writeTo );
        }
    }

    /**
     * Appends a file to the archive after the current entry.
     *
     * One or multiple files can be added directly after the current file.
     * The remaining entries after the current are removed from the archive!
     *
     * The $files can either be a string or an array of strings. Which, respectively, represents a
     * single file or multiple files.
     *
     * $prefix specifies the begin part of the $files path that should not be included in the archive.
     * The files in the archive are always stored relatively.
     *
     * Example:
     * <code>
     * $tar = ezcArchive( "/tmp/my_archive.tar", ezcArchive::TAR );
     *
     * // Append two files to the end of the archive.
     * $tar->seek( 0, SEEK_END );
     * $tar->appendToCurrent( array( "/home/rb/file1.txt", "/home/rb/file2.txt" ), "/home/rb/" );
     * </code>
     *
     * When multiple files are added to the archive at the same time, thus using an array, does not
     * necessarily produce the same archive as repeatively adding one file to the archive.
     * For example, the Tar archive format, can detect that files hardlink to each other and will store
     * it in a more efficient way.
     *
     * @throws ezcArchiveException            if the archive is closed or read-only
     * @throws ezcBaseFileNotFoundException   if one of the specified files is missing
     * @throws ezcBaseFilePermissionException if the archive is not writable
     *
     * @param string|array(string) $files  Array or a single path to a file.
     * @param string $prefix               First part of the path used in $files.
     * @return bool
     */
    public function appendToCurrent( $files, $prefix )
    {
        if ( !$this->isWritable() )
        {
            throw new ezcBaseFilePermissionException( $this->file->getFileName(), ezcBaseFilePermissionException::WRITE, "Archive is read-only" );
        }

        // Current position valid?
        if ( !$this->isEmpty() && !$this->valid() )
        {
            return false;
        }

        if ( !is_array( $files ) )
        {
            $files = array( $files );
        }

        // Check whether the files are correct.
        foreach ( $files as $file )
        {
            if ( !file_exists( $file ) && !is_link( $file ) )
            {
                throw new ezcBaseFileNotFoundException( $file );
            }
        }

        // Search for all the entries, because otherwise hardlinked files show up as an ordinary file.
        $entries = ezcArchiveEntry::getEntryFromFile( $files, $prefix );

        if ( $this->isEmpty() )
        {
            $this->file->truncate();
            $this->file->rewind();
            $cur = -1;
        }
        else
        {
            // Create the new headers.
            $cur = $this->key(); // Current Header.

            $lh = $this->getLocalHeader( $cur );
            $pos = $this->localHeaderPositions[ $cur ] + $lh->getHeaderSize() + $lh->compressedSize;
            $this->file->truncate( $pos );
            $this->file->seek( $pos );
        }

        foreach ( $entries as $entry )
        {
            $cur++;
           // Set local header position
            $this->localHeaderPositions[$cur ] = $this->file->getPosition();

            // Set local header
            $this->localHeaders[ $cur ] =  new ezcArchiveLocalFileHeader();
            $this->localHeaders[ $cur ]->setHeaderFromArchiveEntry( $entry );

            if ( $entry->isSymLink() )
            {
                $fileData = $entry->getLink();
                $this->localHeaders[ $cur ]->setCompression( 0, $this->localHeaders[$cur]->uncompressedSize ); // Compression is 0, for now.
            }
            else if ( $entry->isDirectory() )
            {
                // Added for issue #13517: Not possible to add directories to an archive on Windows
                $fileData = gzdeflate( '' ); // empty string for directories
                $this->localHeaders[ $cur ]->setCompression( 8, strlen( $fileData ) );
            }
            else
            {
                // FIXME, File in memory, compression level always 8, add constants.
                $fileData = gzdeflate( file_get_contents( $entry->getPath() ) );
                $this->localHeaders[ $cur ]->setCompression( 8, strlen( $fileData)  );
            }

            $this->localHeaders[ $cur ]->writeEncodedHeader( $this->file );

            // Write link or file.
            $this->file->write( $fileData );
            unset( $fileData );

            // Create also the central headers.
            $this->centralHeaders[ $cur ] = new ezcArchiveCentralDirectoryHeader();
            $this->centralHeaders[ $cur ]->setHeaderFromLocalFileHeader( $this->localHeaders[ $cur ] );
            $this->centralHeaders[ $cur ]->setHeaderFromArchiveEntry( $entry );
            $this->centralHeaders[ $cur ]->relativeHeaderOffset = $this->localHeaderPositions[ $cur ];

            // Set the entry.
            $entry->removePrefixFromPath();
            $this->entries[ $cur ] = $entry;
        }

        for ( $i = 0; $i <= $cur; $i++ )
        {
            // Write the headers.
            $this->centralHeaderPositions[ $i ] = $this->file->getPosition();
            $this->centralHeaders[ $i ]->writeEncodedHeader( $this->file );
        }

        // Remove the rest of the localHeaders and centralHeaders.
        for ( $i = ( $cur + 1 ); $i < $this->entriesRead; $i++ )
        {
            unset( $this->localHeaders[ $i ] );
            unset( $this->localHeaderPositions[ $i ] );
            unset( $this->centralHeaders[ $i ] );
            unset( $this->centralHeaderPositions[ $i ] );
        }

        $this->entriesRead = $cur + 1;

        // Write the end record.
        $this->endRecord = new ezcArchiveCentralDirectoryEndHeader();
        $this->endRecord->centralDirectoryStart = $this->centralHeaderPositions[0];
        $this->endRecord->centralDirectorySize = $this->file->getPosition() - $this->centralHeaderPositions[0];

        $this->endRecord->totalCentralDirectoryEntries = $cur + 1;
        $this->endRecord->writeEncodedHeader( $this->file );
    }

    /**
     * Appends a file or directory to the end of the archive. Multiple files or directory can
     * be added to the archive when an array is used as input parameter.
     *
     * @see appendToCurrent()
     *
     * @throws ezcArchiveException  if one of the files cannot be written to the archive.
     *
     * @param string|array(string) $files  Array or a single path to a file.
     * @param string $prefix               First part of the path used in $files.
     *
     * @return bool
     */
    public function append( $files, $prefix )
    {
        if ( !$this->isWritable() )
        {
            throw new ezcArchiveException( "Archive is read-only", ezcArchiveException::ARCHIVE_NOT_WRITABLE );
        }

        $this->seek( 0, SEEK_END );
        $this->appendToCurrent( $files, $prefix );
     }

    /**
     * Truncates the archive to $fileNumber of files.
     *
     * The $fileNumber parameter specifies the amount of files that should remain.
     * If the default value, zero, is used then the entire archive file is cleared.
     *
     * @throws ezcBaseFilePermissionException
     *         if the file is read-only
     *         or if the current algorithm cannot write
     * @param int $fileNumber
     * @return bool
     */
    public function truncate( $fileNumber = 0 )
    {
        if ( !$this->isWritable() )
        {
            throw new ezcBaseFilePermissionException( $this->file->getFileName(), ezcBaseFilePermissionException::WRITE );
        }

        $originalFileNumber = $this->fileNumber;

        // Entirely empty the file.
        if ( $fileNumber == 0 )
        {
            $this->file->truncate();
            $this->entriesRead = 0;

            $this->localHeaders = array();
            $this->localHeaderPositions = array();
            $this->centralHeaders = array();
            $this->centralHeaderPositions = array();
            $this->endRecord = null;
        }
        else
        {
            $this->fileNumber = $fileNumber;
            if ( !$this->valid() )
            {
                return false;
            }

            // Truncate the file.
            $pos = $this->localHeaderPositions[ $fileNumber - 1 ];
            $this->file->truncate( $pos );
            $this->file->seek( $pos );

            for ( $i = 0; $i < $fileNumber; $i++ )
            {
                // Write the headers.
                $this->centralHeaders[$i]->writeEncodedHeader( $this->file );
            }

            // Clean up some headers.
            for ( $i = $fileNumber; $i < $this->entriesRead; $i++ )
            {
                unset( $this->localHeaderPositions[$i] );
                unset( $this->centralHeaderPositions[$i] );
                unset( $this->localHeaders[$i] );
                unset( $this->centralHeaders[$i] );
            }

            $this->entriesRead = $fileNumber;

            $this->setEndRecord();
            $this->endRecord->writeEncodedHeader( $this->file );

            $this->fileNumber = $originalFileNumber;
            return $this->valid();
        }
    }

    /**
     * Returns true if it is possible to write to the archive, otherwise false.
     *
     * This method returns false if the archive is read-only, the algorithm
     * didn't implement any write methods, or both.
     *
     * @see algorithmCanWrite()
     *
     * @throws ezcArchiveException
     *         if the archive is closed
     * @return bool
     */
    public function isWritable()
    {
        if ( $this->file === null )
        {
            throw new ezcArchiveException( "The archive is closed" );
        }

        return ( !$this->file->isReadOnly() && $this->algorithmCanWrite() );
    }

    /**
     * Creates and sets a new {@link ezcArchiveCentralDirectoryEndHeader}.
     *
     * The new {@link ezcArchiveCentralDirectoryEndHeader} is based on the current file position,
     * the centralHeaderPositions, and the number of entries read.
     *
     * @return void
     */
    protected function setEndRecord()
    {
        $this->endRecord = new ezcArchiveCentralDirectoryEndHeader();
        $this->endRecord->centralDirectoryStart = $this->centralHeaderPositions[0];
        $this->endRecord->centralDirectorySize = $this->file->getPosition() - $this->centralHeaderPositions[0];

        $this->endRecord->totalCentralDirectoryEntries = $this->entriesRead;
    }
}
?>
