<?php
/**
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.7.1
 * @filesource
 * @package Mail
 */

/**
 * A container to store a Content-Disposition header as described in http://www.faqs.org/rfcs/rfc2183.
 *
 * This container is used on the contentDisposition property on mail parts.
 * Use it for reading and setting the Content-Disposition header.
 *
 * @package Mail
 * @version 1.7.1
 */
class ezcMailContentDispositionHeader extends ezcBaseStruct
{
    /**
     * The disposition type, either "inline" or "attachment".
     *
     * @var string
     */
    public $disposition;

    /**
     * The filename of the attachment.
     *
     * The filename should never include path information.
     *
     * @var string
     */
    public $fileName;

    /**
     * The filename of the attachment, formatted for display. Used only for
     * parsing, not used when generating a mail.
     *
     * The filename should never include path information.
     *
     * Added for issue #13038. If you use __set_state() be sure to set this
     * property also.
     *
     * @var string
     */
    public $displayFileName;

    /**
     * The language of the filename.
     *
     * @var string
     */
    public $fileNameLanguage;

    /**
     * The characterset of the file name.
     *
     * @var string
     */
    public $fileNameCharSet;

    /**
     * The creation date of the file attachment.
     *
     * The time should be formatted as specified by http://www.faqs.org/rfcs/rfc822.html
     * section 5.
     *
     * A typical example is: Sun, 21 May 2006 16:00:50 +0400
     *
     * @var string
     */
    public $creationDate;

    /**
     * The last modification date of the file attachment.
     *
     * The time should be formatted as specified by http://www.faqs.org/rfcs/rfc822.html
     * section 5.
     *
     * A typical example is: Sun, 21 May 2006 16:00:50 +0400
     *
     * @var string
     */
    public $modificationDate;

    /**
     * The last date the file attachment was read.
     *
     * The time should be formatted as specified by http://www.faqs.org/rfcs/rfc822.html
     * section 5.
     *
     * A typical example is: Sun, 21 May 2006 16:00:50 +0400
     *
     * @var string
     */
    public $readDate;

    /**
     * The size of the content in bytes.
     *
     * @var int
     */
    public $size;

    /**
     * Any additional parameters provided in the Content-Disposition header.
     *
     * The format of the field is array(parameterName=>parameterValue)
     *
     * @var array(string=>string)
     */
    public $additionalParameters = array();

    /**
     * Holds language and characterset data for the additional parameters.
     *
     * Format: array(parameterName=>array('charSet'=>string,'language'=>string))
     *
     * @apichange Merge this with $additionalParamters OR come up with an entirely new idea for the ContentDispositionHeader
     * @var array(string=>array())
     */
    public $additionalParametersMetaData = array();

    /**
     * Constructs a new ezcMailContentDispositionHeader holding the various values of this
     * container.
     *
     * @param string $disposition
     * @param string $fileName
     * @param string $creationDate
     * @param string $modificationDate
     * @param string $readDate
     * @param string $size
     * @param array(string=>string) $additionalParameters
     * @param string $fileNameLanguage
     * @param string $fileNameCharSet
     */
    public function __construct( $disposition = 'inline',
                                 $fileName = null,
                                 $creationDate = null,
                                 $modificationDate = null,
                                 $readDate = null,
                                 $size = null,
                                 $additionalParameters = array(),
                                 $fileNameLanguage = null,
                                 $fileNameCharSet = null,
                                 $displayFileName = null )
    {
        $this->disposition = $disposition;
        $this->fileName = $fileName;
        $this->fileNameLanguage = $fileNameLanguage;
        $this->fileNameCharSet = $fileNameCharSet;
        $this->displayFileName = $displayFileName;
        $this->creationDate = $creationDate;
        $this->modificationDate = $modificationDate;
        $this->readDate = $readDate;
        $this->size = $size;
        $this->additionalParameters = $additionalParameters;
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
     * @return ezcMailAddress
     */
    static public function __set_state( array $array )
    {
        return new ezcMailContentDispositionHeader( $array['disposition'],
                                                    $array['fileName'],
                                                    $array['creationDate'],
                                                    $array['modificationDate'],
                                                    $array['readDate'],
                                                    $array['size'],
                                                    $array['additionalParameters'],
                                                    $array['fileNameLanguage'],
                                                    $array['fileNameCharSet'],
                                                    $array['displayFileName']
                                                    );
    }
}

?>
