<?php
/**
 * File containing the ezcMvcResultContentDisposition class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * This struct contains content disposition meta-data
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcResultContentDisposition extends ezcBaseStruct
{
    /**
     * The disposition type (inline or attachment)
     *
     * @var string
     */
    public $type;

    /**
     * The filename parameter, encoded as a UTF-8 string.
     *
     * @var string
     */
    public $filename;

    /**
     * The creation date parameter
     *
     * @var DateTime
     */
    public $creationDate;

    /**
     * The modification date parameter
     *
     * @var DateTime
     */
    public $modificationDate;

    /**
     * The read date parameter
     *
     * @var DateTime
     */
    public $readDate;

    /**
     * The size parameter
     *
     * @var int
     */
    public $size;

    /**
     * Constructs a new ezcMvcResultContent.
     *
     * @param string $type
     * @param string $filename
     * @param DateTime $creationDate
     * @param DateTime $modificationDate
     * @param DateTime $readDate
     * @param int $size
     */
    public function __construct( $type = 'inline', $filename = null,
        DateTime $creationDate = null, DateTime $modificationDate = null,
        DateTime $readDate = null,
        $size = null )
    {
        $this->type = $type;
        $this->filename = $filename;
        $this->creationDate = $creationDate;
        $this->modificationDate = $modificationDate;
        $this->readDate = $readDate;
        $this->size = $size;
    }

    /**
     * Returns a new instance of this class with the data specified by $array.
     *
     * $array contains all the data members of this class in the form:
     * array('member_name'=>value).
     *
     * __set_state makes this class exportable with var_export.
     * var_export() generates code, that calls this method when it
     * is parsed with PHP.
     *
     * @param array(string=>mixed) $array
     * @return ezcMvcResultContent
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcResultContent( $array['type'], $array['filename'],
            $array['creationDate'], $array['modificationDate'],
            $array['readDate'], $array['size'] );
    }
}
?>
