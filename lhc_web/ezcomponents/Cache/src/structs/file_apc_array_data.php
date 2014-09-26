<?php
/**
 * File containing the ezcCacheStorageFileApcArrayDataStruct class.
 *
 * @package Cache
 * @version 1.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Defines a file array APC Storage structure.
 *
 * @package Cache
 * @version 1.5
 */
class ezcCacheStorageFileApcArrayDataStruct extends ezcBaseStruct
{
    /**
     * Holds the actual data.
     *
     * @var mixed
     */
    public $data;

    /**
     * Holds the time the data was introduced in the cache.
     *
     * @var int
     */
    public $time;

    /**
     * Holds the location of the cache.
     *
     * @var string
     */
    public $location;

    /**
     * Holds the modified time of the file.
     *
     * @var int|bool
     */
    public $mtime;

    /**
     * Holds the accessed time of the file.
     *
     * @var int|bool
     */
    public $atime;

    /**
     * Constructs a new ezcCacheStorageFileApcArrayDataStruct.
     *
     * @param mixed $data
     * @param string $location
     * @param int|bool $mtime
     * @param int|bool $atime
     */
    public function __construct( $data, $location, $mtime = false, $atime = false )
    {
        $this->data = $data;
        $this->location = $location;
        $this->mtime = $mtime;
        $this->atime = $atime;
        $this->time = time();
    }
}
?>
