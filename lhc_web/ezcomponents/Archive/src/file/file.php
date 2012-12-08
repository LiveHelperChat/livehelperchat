<?php
/**
 * File containing the ezcArchiveFile class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcArchiveFile should implement the common interface between the
 * ezcArchiveBlockFile and ezcArchiveCharacterFile.
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
abstract class ezcArchiveFile implements Iterator
{
    /**
     * The file is read-only.
     * The file permissions can be set to read-only or file is compressed with a
     * stream that can only be read. E.g. bzip2.
     */
    const READ_ONLY   = 1;

    /**
     * The file is write-only.
     * The file permissions can be set to write-only or file should be compressed with a
     * stream that can only write. E.g. bzip2.
     */
    const WRITE_ONLY  = 2;

    /**
     * The file is either read or append mode.
     * Some compressed streams (zlib) do not support reading and writing. But seperate reading
     * and appending does work.
     */
    const READ_APPEND = 3;

    /**
     * The file is opened in a read and write mode.
     */
    const READ_WRITE  = 4;

    /**
     * The mode the file is opened in. It has one of the following constant values:
     * READ_ONLY, WRITE_ONLY, READ_APPEND, or READ_WRITE.
     *
     * @var int
     */
    protected $fileAccess = null;

    /**
     * The current resource of the opened file.
     * If the file is closed, this resource should point to NULL.
     *
     * @var resource
     */
    protected $fp = null;

    /**
     * The name of the file.
     *
     * @var string
     */
    protected $fileName;

    /**
     * True when the current file does not have any blocks, otherwise false.
     *
     * @var boolean
     */
    protected $isEmpty;

    /**
     * True if the file-pointer supports seeking, otherwise false.
     * For example, files that use the bzip2 stream cannot seek.
     *
     * @var boolean
     */
    protected $fileMetaData;

    /**
     * True when the current block is valid, otherwise false.
     *
     * @var boolean
     */
    protected $isValid = false;

    /**
     * Read-mode for the archive file.
     */
    const SWITCH_READ = 0;

    /**
     * Append-mode for the archive file.
     */
    const SWITCH_APPEND = 1;

    /**
     * Switch for read-mode and append-mode.
     *
     * @var int
     */
    protected $readAppendSwitch;

    /**
     * Is the file new.
     *
     * @var bool
     */
    protected $isNew;

    /**
     * Is the file modified.
     *
     * @var bool
     */
    protected $isModified;

    /**
     * Opens the specified archive.
     *
     * If $createIfNotExist is true, then the file will be created if it does
     * not exist.
     *
     * @param string $fileName
     * @param bool $createIfNotExist
     * @param bool $readOnly
     * @return bool
     */
    protected function openFile( $fileName, $createIfNotExist, $readOnly = false )
    {
        if ( !$readOnly && $createIfNotExist && !self::fileExists( $fileName ) )
        {
            $this->isNew = true;
            $this->isEmpty = true;
            if ( !self::touch( $fileName ) )
            {
                throw new ezcBaseFilePermissionException( self::getPureFileName( $fileName ), ezcBaseFilePermissionException::WRITE );
            }
        }
        else
        {
            $this->isNew = false;
        }

        // Try to open it in read and write mode.
        $opened = false;
        if ( !$readOnly ) 
        {
            $this->fp = @fopen( $fileName, "r+b" );
            if ( $this->fp )
            {
                $this->fileAccess = self::READ_WRITE;
                $opened = true;
            }
        }

        if ( !$opened )
        {
            // Try to open it in read-only mode.
            $this->fp = @fopen( $fileName, "rb" );
            $this->fileAccess = self::READ_ONLY;

            // Check if we opened the file.
            if ( !$this->fp )
            {
                if ( !self::fileExists( $fileName ) )
                {
                    throw new ezcBaseFileNotFoundException( $fileName );
                }

                // Cannot read the file.
                throw new ezcBaseFilePermissionException( $fileName, ezcBaseFilePermissionException::READ );
            }
        }

        $this->fileMetaData = stream_get_meta_data( $this->fp );

        // Hardcode BZip2 to read-only.
        // For some reason we can open the file in read-write mode, but we cannot rewind the fp. Strange..
        if ( $this->fileMetaData["wrapper_type"] == "BZip2" )
        {
            $this->fileAccess = self::READ_ONLY;
        }

        // Why is it read only?
        if ( !$readOnly && $this->fileAccess == self::READ_ONLY )
        {
            if ( $this->fileMetaData["wrapper_type"] == "ZLIB" || $this->fileMetaData["wrapper_type"] == "BZip2" )
            {
                // Append mode available?
                $b = @fopen( $fileName, "ab" );
                if ( $b !== false )
                {
                    // We have also a write-only mode.
                    fclose( $b );

                    // The file is either read-only or write-only.
                    $this->fileAccess = self::READ_APPEND;
                    $this->readAppendSwitch = self::SWITCH_READ;
                }
                else
                {
                    // Maybe we should write only to the archive.
                    // Test this only, when the archive is new.

                    if ( $this->isNew )
                    {
                        $b = @fopen( $fileName, "wb" );
                        if ( $b !== false )
                        {
                            // XXX Clean up.
                            $this->fp = $b;

                            $this->isEmpty = true;
                            $this->fileAccess = self::WRITE_ONLY;

                            $this->fileName = $fileName;
                            $this->isModified = false;

                            return true;
                        }
                    }
                }
            }
        }

        // Check if the archive is empty.
        if ( fgetc( $this->fp ) === false )
        {
            $this->isEmpty = true;
        }
        else
        {
            $this->rewind();
            $this->isEmpty = false;
        }

        $this->fileName = $fileName;
        $this->isModified = false;
    }

    /**
     * Returns the file name or file path.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Switch to write mode.
     */
    public function switchWriteMode()
    {
        // Switch only when we are in read (only) mode.
        if ( $this->fileAccess == self::READ_APPEND && $this->readAppendSwitch == self::SWITCH_READ )
        {
            fclose( $this->fp );
            $this->fp = @fopen( $this->fileName, "ab" );
            if ( $this->fp === false )
            {
                throw new ezcBaseFilePermissionException( self::getPureFileName( $this->fileName ), ezcBaseFilePermissionException::WRITE, "Cannot switch to write mode" );
            }
            $this->readAppendSwitch = self::SWITCH_APPEND;
        }
    }

    /**
     * Switch to read mode.
     *
     * @param int $pos Position to seek to; not used
     */
    public function switchReadMode( $pos = 0 )
    {
        // Switch only when we are in write (only) mode.
        if ( $this->fileAccess == self::READ_APPEND && $this->readAppendSwitch == self::SWITCH_APPEND )
        {
            fclose( $this->fp );

            $this->fp = fopen( $this->fileName, "rb" );

            if ( $this->fp === false )
            {
                throw new ezcBaseFilePermissionException( self::getPureFileName( $this->fileName ), ezcBaseFilePermissionException::READ, "Cannot switch back to read mode" );
            }
            $this->readAppendSwitch = self::SWITCH_READ;

            $this->positionSeek( 0, SEEK_END );

            // Doesn't Make sense, Seek-end should be at the end!
            while ( fgetc( $this->fp ) !== false );
        }
    }

    /**
     * Returns if the file access is in append mode.
     *
     * @return bool
     */
    public function isReadOnlyWriteOnlyStream()
    {
        return $this->fileAccess == self::READ_APPEND;
    }



    /**
     * Touches the specified file (sets the access and modification time).
     *
     * PHP system touch doesn't work correctly with the compress.zlib file.
     *
     * @param string $fileName
     * @return bool
     */
    public static function touch( $fileName )
    {
        return touch( self::getPureFileName( $fileName ) );
    }

    /**
     * Returns if the specified file exists.
     *
     * @param string $fileName
     * @return bool
     */
    public static function fileExists( $fileName )
    {
        return file_exists( self::getPureFileName( $fileName ) );
    }

    /**
     * Returns the specified file name without any filters or compression stream.
     *
     * @param string $fileName
     * @return string
     */
    private static function getPureFileName( $fileName )
    {
        // TODO: Multistream goes wrong.
        if ( strncmp( $fileName, "compress.zlib://", 16 ) == 0 )
        {
            return substr( $fileName, 16 );
        }

        if ( strncmp( $fileName, "compress.bzip2://", 17 ) == 0 )
        {
            return substr( $fileName, 17 );
        }

        return $fileName;
    }

    /**
     * Rewind the current file, and the current() method will return the
     * data from the first block, if available.
     */
    public function rewind()
    {
        if ( !is_null( $this->fp ) )
        {
            $this->isValid = true;

            if ( !$this->fileMetaData["seekable"] )
            {
                fclose( $this->fp );
                $this->fp = fopen( $this->fileMetaData["uri"], $this->fileMetaData["mode"] );
            }
            else
            {
                rewind( $this->fp );
            }

            $this->next();
        }
        else
        {
            $this->isValid = false;
        }
    }

    /**
     * Seeks in the file to/by the specified position.
     *
     * Ways of seeking ($whence):
     * - SEEK_SET - $pos is absolute, seek to that position in the file
     * - SEEK_CUR - $pos is relative, seek by $pos bytes from the current position
     *
     * @throws ezcArchiveException
     *         if trying to use SEEK_END for $whence
     * @param int $pos
     * @param int $whence
     * @return int If seek was successful or not
     */
    protected function positionSeek( $pos, $whence = SEEK_SET )
    {
        // Seek the end of the file in a write only file always succeeds.
        if ( $this->fileAccess == self::WRITE_ONLY && $pos == 0 && $whence == SEEK_END )
        {
            return true;
        }

        if ( $this->fileMetaData["seekable"] )
        {
            /**
             * Ugh, for some reason fseek starts throwing warnings for
             * zlib streams with SEEK_END. And there is no way to know this
             * upfront, so we need to use @ here. #fail.
             */
            return @fseek( $this->fp, $pos, $whence );
        }
        else
        {
            switch ( $whence )
            {
                case SEEK_SET:
                    $transPos = $pos;
                    break;

                case SEEK_CUR:
                    $transPos = $pos + ftell( $this->fp );
                    break;

                case SEEK_END:
                    throw new ezcArchiveException( "SEEK_END in a non-seekable file is not supported (yet)." );
            }

            $cur = ftell( $this->fp );
            if ( $transPos <  $cur )
            {
                fclose( $this->fp );
                $this->fp = fopen( $this->fileMetaData["uri"], $this->fileMetaData["mode"] );

                $cur = 0;
            }

            for ( $i = $cur; $i < $transPos; $i++ )
            {
                $c = fgetc( $this->fp );
                if ( $c === false )
                {
                    return -1;
                }
            }

            return 0;
        }
    }

    /**
     * Returns the current file access mode.
     *
     * @var int
     */
    public function getFileAccess()
    {
        return $this->fileAccess;
    }

    /**
     * Returns if the file is in read-only mode.
     *
     * @var bool
     */
    public function isReadOnly()
    {
        return $this->fileAccess == self::READ_ONLY;
    }

    /**
     * Returns if the file is new.
     *
     * @var bool
     */
    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * Returns if the file is modified.
     *
     * @var bool
     */
    public function isModified()
    {
        return $this->isModified;
    }

    /**
     * Closes the file.
     */
    public function close()
    {
        if ( is_resource( $this->fp ) )
        {
            fclose( $this->fp );
            $this->fp = null;
        }
    }
}
?>
