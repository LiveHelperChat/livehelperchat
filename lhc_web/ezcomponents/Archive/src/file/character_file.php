<?php
/**
 * File contains the ezcArchiveCharacterFile class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * The ezcArchiveCharacterFile class provides an interface for reading from and writing to a file.
 *
 * The file is opened via in constructor and closed via the destructor.
 *
 * The iterator functionality can be used to read characters from and append characters to the file,
 * so it has the same behaviour as the {@link ezcArchiveBlockFile}.
 *
 * @package Archive
 * @version 1.4.1
 * @access private
 */
class ezcArchiveCharacterFile extends ezcArchiveFile
{
    /**
     * The current character.
     *
     * @todo FIXME
     *
     * @var string
     */
    private $character;

    /**
     * The current character position.
     *
     * @var int
     */
    private $position;

    /**
     * Sets the property $name to $value.
     *
     * Because there are no properties available, this method will always
     * throw an {@link ezcBasePropertyNotFoundException}.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @param mixed $value
     * @return void
     * @ignore
     */
    public function __set( $name, $value )
    {
        throw new ezcBasePropertyNotFoundException( $name );
    }

    /**
     * Returns the property $name.
     *
     * Because there are no properties available, this method will always
     * throw an {@link ezcBasePropertyNotFoundException}.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        throw new ezcBasePropertyNotFoundException( $name );
    }

    /**
     * Constructs a new ezcArchiveBlockFile.
     *
     * The given file name is tried to be opened in read / write mode.
     * If that fails, the file will be opened in read-only mode.
     *
     * If the bool $createIfNotExist is set to true, it will create the file if
     * it doesn't exist.
     *
     * @throws ezcArchiveException if the file cannot be found or opened for any reason.
     *
     * @param string $fileName
     * @param bool $createIfNotExist
     * @param bool $readOnly
     */
    public function __construct( $fileName, $createIfNotExist = false, $readOnly = false )
    {
        $this->openFile( $fileName, $createIfNotExist, $readOnly );
        $this->rewind();
    }

    /**
     * The destructor will close all open files.
     */
    public function __destruct()
    {
        if ( $this->fp )
        {
            fclose( $this->fp );
        }
    }

    /**
     * Rewinds the current file.
     *
     * @return void
     */
    public function rewind()
    {
        parent::rewind();

        $this->position = -1;
    }

    /**
     * Returns the current character if available.
     *
     * If the character is not available, the value false is returned.
     *
     * @return string
     */
    public function current()
    {
        return ( $this->isValid ? $this->character : false );
    }

    /**
     * Iterates to the next character.
     *
     * Returns the next character if it exists; otherwise returns false.
     *
     * @return string
     */
    public function next()
    {
        if ( $this->isValid  )
        {
            $this->character = fgetc( $this->fp );

            if ( $this->character === false )
            {
                $this->isValid = false;
                return false;
            }

            $this->position++;
            return $this->character;
        }

        return false;
    }

    /**
     * Returns the current position.
     *
     * The first position has the value zero.
     *
     * @return int
     */
    public function key()
    {
        return ( $this->isValid ? ftell( $this->fp ) - 1 : false );
    }

    /**
     * Returns the file-pointer position.
     *
     * @todo FIXME Is this or the key() function needed?
     *
     * return int
     */
    public function getPosition()
    {
        return ftell( $this->fp );
    }

    /**
     * Returns true if the current character is valid, otherwise false.
     *
     * @return bool
     */
    public function valid()
    {
        return $this->isValid;
    }

    /**
     * Appends the string $data after the current position.
     *
     * The character(s) after the current position are removed and the $data will be
     * appended.
     * To start from the beginning of the file, call first the truncate() method.
     *
     * @throws  ezcBaseFilePermissionException if the file is opened in read-only mode.
     *
     * @param  string $data
     * @return int
     */
    public function append( $data )
    {
        if ( $this->fileAccess == self::READ_ONLY )
        {
            throw new ezcBaseFilePermissionException( $this->fileName, ezcBaseFilePermissionException::WRITE, "The archive is opened in a read-only mode." );
        }

        $pos = ftell( $this->fp );
        ftruncate( $this->fp, $pos );
        $length = $this->writeBytes( $data );

        $this->isValid = true;
        if ( $this->isEmpty )
        {
            rewind( $this->fp );
            $this->next();
        }
        else
        {
            $this->positionSeek( $pos, SEEK_SET );
        }

        $this->isEmpty = false;

        return $length;
    }

    /**
     * Writes the given string $data to the current file.
     *
     * This method tries to write the $data to the file. Upon failure, this method
     * will retry, until no progress is made anymore. And eventually it will throw
     * an exception.
     *
     * @throws ezcBaseFileIoException if it is not possible to write to the file.
     *
     * @param string $data
     * @return void
     */
    protected function writeBytes( $data )
    {
        $dl = strlen( $data );
        if ( $dl == 0 )
        {
            return; // No bytes to write.
        }

        $wl = fwrite( $this->fp, $data );

        // Partly written? For example an interrupt can occur when writing a remote file.
        while ( $dl > $wl && $wl != 0 )
        {
            // retry, until no progress is made.
            $data = substr( $data, $wl );

            $dl = strlen( $data );
            $wl = fwrite( $this->fp, $data );
        }

        if ( $wl == 0 )
        {
            throw new ezcBaseFileIoException ( $this->fileName, ezcBaseFileIoException::WRITE, "Retried to write, but no progress was made. Disk full?" );
        }

        return $wl;
    }

    /**
     * Truncates the current file to the number of characters $position.
     *
     * If $position is zero, the entire block file will be truncated. After the file is truncated,
     * make sure the current block position is valid. So, do a rewind() after
     * truncating the entire block file.
     *
     * @param int $position
     * @return void
     */
    public function truncate( $position = 0 )
    {
        ftruncate( $this->fp, $position );

        if ( $position == 0 )
        {
            $this->isEmpty = true;
        }

        if ( $this->position > $position )
        {
            $this->isValid = false;
        }
    }

    /**
     * Sets the current character position.
     *
     * Sets the current character position. The new position is obtained by adding
     * the $offset amount of characters to the position specified by $whence.
     *
     * These values are:
     *  SEEK_SET: The first character,
     *  SEEK_CUR: The current character position,
     *  SEEK_END: The last character.
     *
     * The blockOffset can be negative.
     *
     * @param int $offset
     * @param int $whence
     * @return void
     */
    public function seek( $offset, $whence = SEEK_SET )
    {
        $this->isValid = true;

        $pos = $offset;
        /*
        if ( $whence == SEEK_END || $whence == SEEK_CUR )
        {
            if ( !$this->isEmpty() )
            {
                $pos -= 1;
            }
        }
         */


        if ( $this->positionSeek( $pos, $whence ) == -1 )
        {
            $this->isValid = false;
        }

        $this->position = $pos - 1;
        $this->next(); // Will set isValid to false, if blockfile is empty.
    }

    /**
     * Returns true if the file is empty, otherwise false.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->isEmpty;
    }

    /**
     * Reads current character plus extra. Forward the current pointer.
     *
     * @param int $bytes
     * @return string
     */
    public function read( $bytes )
    {
        if ( $bytes < 1 )
        {
            return false;
        }

        $data = $this->character;
        if ( $bytes == 1 )
        {
            $this->next();
            return $data;
        }

        $data .= fread( $this->fp, $bytes - 1 );

        $this->position += $bytes - 1;

        if ( $data === false )
        {
           $this->isValid = false;
           return false;
        }
        else
        {
            $this->next();
        }
        return $data;
    }

    /**
     * Writes the specified data and returns the length of the written data.
     *
     * FIXME, maybe valid() when at the eof.
     * FIXME. Write current character plus extra.
     * FIXME.. slow.
     *
     * @throws ezcBaseFilePermissionException
     *         if the file access is read-only
     * @param string $data
     * @return int
     */
    public function write( $data )
    {
        if ( $this->fileAccess == self::READ_ONLY )
        {
            throw new ezcBaseFilePermissionException( $this->fileName, ezcBaseFilePermissionException::WRITE, "The archive is opened in a read-only mode." );
        }

        $pos = ftell( $this->fp );
        if ( $this->valid() )
        {
            $pos--;
        }

        fseek( $this->fp, $pos );
        ftruncate( $this->fp, $pos );
        $length = $this->writeBytes( $data );

        $this->isValid = false;
        $this->isEmpty = false;

        return $length;
    }
}
?>
