<?php
/**
 * File containing the ezcMailStorageSet class
 *
 * @package Mail
 * @version 1.7.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * ezcMailStorageSet is a wrapper around other mail sets and provides saving of
 * mail sources to files.
 *
 * Example:
 *
 * <code>
 * // create a new POP3 transport object and a mail parser object
 * $transport = new ezcMailPop3Transport( "server" );
 * $transport->authenticate( "username", "password" );
 * $parser = new ezcMailParser();
 *
 * // wrap around the set returned by fetchAll()
 * // and specify that the sources are to be saved in the folder /tmp/cache
 * $set = new ezcMailStorageSet( $transport->fetchAll(), '/tmp/cache' );
 *
 * // parse the storage set
 * $mail = $parser->parseMail( $set );
 *
 * // get the filenames of the saved mails in the set.
 * // The file names are composed of process ID + current time + a counter
 * // This array must be saved to be used on a subsequent request
 * $files = $set->getSourceFiles();
 *
 * // get the source of the 4th saved mail.
 * // This can be on a subsequent request if the $files array was saved from
 * // a previous request
 * $source = file_get_contents( $files[3] );
 * </code>
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailStorageSet implements ezcMailParserSet
{
    /**
     * Holds the pointer to the current file which holds the mail source.
     *
     * @var filepointer
     */
    private $writer = null;

    /**
     * Holds the temporary file name where contents are being initially written
     * (until set is parsed and Message-ID is extracted).
     *
     * @var string
     */
    private $file = null;

    /**
     * Holds the path where the files are written (specified in the constructor).
     *
     * @var string
     */
    private $path = null;

    /**
     * This variable is true if there is more data in the mail that is being fetched.
     *
     * @var bool
     */
    private $hasMoreMailData = false;

    /**
     * Holds the location where to store the message sources.
     *
     * @var string
     */
    private $location;

    /**
     * Holds the filenames holding the sources of the mails in this set.
     *
     * @var array(string)
     */
    private $files = null;

    /**
     * Holds the current email number being parsed.
     *
     * @var int
     */
    private $counter;

    /**
     * Constructs a new storage set around the provided set.
     *
     * $location specifies where to save the message sources. This directory MUST
     * exist and must be writable.
     *
     * @param ezcMailParserSet $set
     * @param string $location
     */
    public function __construct( ezcMailParserSet $set, $location )
    {
        $this->set = $set;
        $this->location = $location;
        $this->path = rtrim( $this->location, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        $this->hasMoreMailData = false;
        $this->counter = 0;
    }

    /**
     * Destructs the set.
     *
     * Closes any open files.
     */
    public function __destruct()
    {
        if ( is_resource( $this->writer ) )
        {
            fclose( $this->writer );
            $this->writer = null;
        }
    }

    /**
     * Returns one line of data from the current mail in the set.
     *
     * Null is returned if there is no current mail in the set or
     * the end of the mail is reached,
     *
     * It also writes the line of data to the current file. If the line contains
     * a Message-ID header then the value in the header will be used to rename the
     * file.
     *
     * @return string
     */
    public function getNextLine()
    {
        if ( $this->hasMoreMailData === false )
        {
            $this->nextMail();
            $this->hasMoreMailData = true;
        }

        $line = $this->set->getNextLine();
        fputs( $this->writer, $line );
        return $line;
    }

    /**
     * Moves the set to the next mail and returns true upon success.
     *
     * False is returned if there are no more mail in the set.
     *
     * @return bool
     */
    public function nextMail()
    {
        if ( $this->writer !== null )
        {
            fclose( $this->writer );
            $this->files[] = $this->path . $this->file;
            $this->writer = null;
        }
        $mail = $this->set->nextMail();
        if ( $mail === true || $this->hasMoreMailData === false )
        {
            $this->counter++;

            // Temporary file name for the mail source
            $this->file = getmypid() . '-' . time() . '-' . $this->counter;
            $writer = fopen( $this->path . $this->file, 'w' );
            if ( $writer !== false )
            {
                $this->writer = $writer;
            }
            return $mail;
        }
        return false;
    }

    /**
     * Returns whether the set has mails.
     *
     * @return bool
     */
    public function hasData()
    {
        return $this->set->hasData();
    }

    /**
     * Returns an array of the filenames holding the sources of the mails in this set.
     *
     * The format of the returned array is:
     * array( 0 => 'location/filename1', 1 => 'location/filename2',...)
     *
     * @return array(string)
     */
    public function getSourceFiles()
    {
        return $this->files;
    }
}
?>
