<?php
/**
 * File contains the ezcArchiveGnuTar class.
 *
 * @package Archive
 * @version 1.4.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * The ezcArchiveGnuTar class implements the GNU Tar archive format.
 *
 * ezcArchiveGnuTar is a subclass from {@link ezcArchive} that provides the common interface,
 * and {@link ezcArchiveUstarTar} that provides the basic Tar implementation.
 *
 * ezcArchiveGnuTar reads on creation only the first {@link ezcArchiveEntry entry} from the archive.
 * When needed next entries are read.
 *
 * The Gnu Tar algorithm is an extension of Ustar Tar. Gnu has the following extended features compared to Ustar:
 * - Filenames of unlimited size.
 * - File size is unlimited.
 *
 * The current implementation allows only reading from a Gnu archive.
 *
 * The features of Gnu Tar and Pax Tar are quite similar, although their approach is different.
 *
 * @package Archive
 * @version 1.4.1
 */
class ezcArchiveGnuTar extends ezcArchiveUstarTar
{
    /**
     * Initializes the Tar and tries to read the first entry from the archive.
     *
     * At initialization it sets the blockFactor to $blockFactor. Each tar archive
     * has always $blockFactor of blocks ( 0, $blockFactor, 2 * $blockFactor, etc ).
     *
     * The Tar archive works with blocks, so therefore the first parameter expects
     * the archive as a blockFile.
     *
     * @param ezcArchiveBlockFile $blockFile
     * @param int $blockFactor
     */
    public function __construct( ezcArchiveBlockFile $blockFile, $blockFactor = 20 )
    {
        parent::__construct( $blockFile, $blockFactor );
    }

    /**
     * Returns the value which specifies a TAR_GNU algorithm.
     *
     * @return int
     */
    public function getAlgorithm()
    {
        return self::TAR_GNU;
    }

    /**
     * Returns false because the TAR_PAX algorithm cannot write (yet).
     *
     * @see isWritable()
     *
     * @return bool
     */
    public function algorithmCanWrite()
    {
        return false;
    }

    /**
     * Creates the a new pax tar header for this class.
     *
     * This method expects an {@link ezcArchiveBlockFile} that points to the header that should be
     * read (and created). If null is given as  block file, an empty header will be created.
     *
     * @param string|null $file
     * @return ezcArchiveGnuHeader
     */
    protected function createTarHeader( $file = null)
    {
        return new ezcArchiveGnuHeader( $file );
    }
}
?>
