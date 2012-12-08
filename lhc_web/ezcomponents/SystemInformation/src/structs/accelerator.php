<?php
/**
 * File containing the ezcSystemInfoAccelerator structure.
 *
 * @package SystemInformation
 * @version 1.0.8
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * A container to store information about a PHP accelerator.
 *
 * This structure used to represent information about a PHP accelerator parameters
 * {@link self::name name}
 * {@link self::url url}
 * {@link self::isEnabled isEnabled}
 * {@link self::versionInt versionInt}
 * {@link self::versionString versionString}
 *
 * @see ezcSystemInfo
 *
 * @package SystemInformation
 * @version 1.0.8
 */
class ezcSystemInfoAccelerator extends ezcBaseStruct
{
    /**
     * Name of PHP accelerator.
     *
     * @var string
     */
    public $name;

    /**
     * URL of the site of PHP accelerator developer.
     *
     * @var string
     */
    public $url;

    /**
     * Flag that informs if PHP accelerator enabled or not.
     *
     * @var bool
     */
    public $isEnabled;

    /**
     * PHP accelerator version number.
     *
     * @var int
     */
    public $versionInt;

    /**
     * PHP accelerator version number as a string.
     *
     * @var string
     */
    public $versionString;

    /**
     * Initialize all structure fields with values.
     *
     * @param string $name
     * @param string $url
     * @param bool   $isEnabled
     * @param int    $versionInt
     * @param string $versionString
     */
    public function __construct( $name, $url, $isEnabled, $versionInt, $versionString )
    {
        $this->name = $name;
        $this->url  = $url;
        $this->isEnabled     = $isEnabled;
        $this->versionInt    = $versionInt;
        $this->versionString = $versionString;
    }
}
?>
