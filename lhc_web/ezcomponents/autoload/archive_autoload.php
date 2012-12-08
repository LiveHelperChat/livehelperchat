<?php
/**
 * Autoloader definition for the Archive component.
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.4.1
 * @filesource
 * @package Archive
 */

return array(
    'ezcArchiveException'                 => 'Archive/exceptions/exception.php',
    'ezcArchiveBlockSizeException'        => 'Archive/exceptions/block_size.php',
    'ezcArchiveChecksumException'         => 'Archive/exceptions/checksum.php',
    'ezcArchiveEmptyException'            => 'Archive/exceptions/empty.php',
    'ezcArchiveEntryPrefixException'      => 'Archive/exceptions/entry_prefix.php',
    'ezcArchiveInternalException'         => 'Archive/exceptions/internal_exception.php',
    'ezcArchiveIoException'               => 'Archive/exceptions/io.php',
    'ezcArchiveUnknownTypeException'      => 'Archive/exceptions/unknown_type.php',
    'ezcArchiveValueException'            => 'Archive/exceptions/value.php',
    'ezcArchive'                          => 'Archive/archive.php',
    'ezcArchiveV7Header'                  => 'Archive/tar/headers/v7.php',
    'ezcArchiveV7Tar'                     => 'Archive/tar/v7.php',
    'ezcArchiveFile'                      => 'Archive/file/file.php',
    'ezcArchiveLocalFileHeader'           => 'Archive/zip/headers/local_file.php',
    'ezcArchiveUstarHeader'               => 'Archive/tar/headers/ustar.php',
    'ezcArchiveUstarTar'                  => 'Archive/tar/ustar.php',
    'ezcArchiveBlockFile'                 => 'Archive/file/block_file.php',
    'ezcArchiveCallback'                  => 'Archive/interfaces/callback.php',
    'ezcArchiveCentralDirectoryEndHeader' => 'Archive/zip/headers/central_directory_end.php',
    'ezcArchiveCentralDirectoryHeader'    => 'Archive/zip/headers/central_directory.php',
    'ezcArchiveCharacterFile'             => 'Archive/file/character_file.php',
    'ezcArchiveChecksums'                 => 'Archive/utils/checksums.php',
    'ezcArchiveEntry'                     => 'Archive/entry.php',
    'ezcArchiveFileStructure'             => 'Archive/structs/file.php',
    'ezcArchiveFileType'                  => 'Archive/utils/file_type.php',
    'ezcArchiveGnuHeader'                 => 'Archive/tar/headers/gnu.php',
    'ezcArchiveGnuTar'                    => 'Archive/tar/gnu.php',
    'ezcArchiveOptions'                   => 'Archive/options/archive.php',
    'ezcArchivePaxHeader'                 => 'Archive/tar/headers/pax.php',
    'ezcArchivePaxTar'                    => 'Archive/tar/pax.php',
    'ezcArchiveStatMode'                  => 'Archive/utils/stat_mode.php',
    'ezcArchiveZip'                       => 'Archive/zip/zip.php',
);
?>
