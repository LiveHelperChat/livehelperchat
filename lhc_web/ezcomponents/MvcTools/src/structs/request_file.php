<?php
/**
 * File containing the ezcMvcRequestFile class
 *
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.3
 * @filesource
 * @package MvcTools
 */

/**
 * Struct which holds a file bundled with the request.
 *
 * @package MvcTools
 * @version 1.1.3
 */
class ezcMvcRequestFile extends ezcBaseStruct
{
    /**
     * File mimetype.
     *
     * @var string
     */
    public $mimeType;

    /**
     * File name.
     *
     * @var string
     */
    public $name;

    /**
     * File size.
     *
     * @var int
     */
    public $size;

    /**
     * Status of the upload.
     *
     * @todo Mention what the different status codes mean.
     * @var mixed
     */
    public $status;

    /**
     * Temporary file path.
     *
     * @var string
     */
    public $tmpPath;

    /**
     * Constructs a new ezcMvcRequestFile.
     *
     * @param string $mimeType
     * @param string $name
     * @param int $size
     * @param mixed $status
     * @param string $tmpPath
     */
    public function __construct( $mimeType = '', $name = '',
        $size = 0, $status = null, $tmpPath = '' )
    {
        $this->mimeType = $mimeType;
        $this->name = $name;
        $this->size = $size;
        $this->status = $status;
        $this->tmpPath = $tmpPath;
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
     * @return ezcMvcRequestFile
     */
    static public function __set_state( array $array )
    {
        return new ezcMvcRequestFile( $array['mimeType'], $array['name'],
            $array['size'], $array['status'], $array['tmpPath'] );
    }
}
?>
