<?php
/**
 * File containing the ezcConfigurationValidationResult class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Provides the result of an ezcConfigurationFileReader::validate() operation.
 *
 * It contains the result of the validation process. The reader will create the
 * result with location information and the validation items which is returned to
 * the caller.
 *
 * Instantiate an object of this class and append items using
 * appendItem(). The caller can then access the result using getResultList().
 *
 * The reader will typically create the result with:
 * <code>
 * $validationError = new ezcConfigurationValidationItem(
 *     ezcConfigurationValidationItem::ERROR,
 *     'test-file.php', 2, 4, 'typo', 'One ] too many'
 * );
 *
 * $result = new ezcConfigurationValidationResult(
 *     "settings", "site", "settings/site.ini"
 * );
 *
 * $result->appendItem( $validationError );
 * </code>
 *
 * After the reader is done parsing, the validation results can be examined with:
 * <code>
 * echo "Warnings: ", $result->getWarningCount(), "\n";
 * echo "Errors:   ", $result->getErrorCount(), "\n";
 * foreach ( $result->getResultList() as $item )
 * {
 *     printf( "In '%s' on line '%d', position: '%d': %s\n",
 *         $item->file, $item->line, $item->column, $item->details
 *     );
 * }
 * </code>
 *
 * @package Configuration
 * @version 1.3.5
 */
class ezcConfigurationValidationResult
{
    /**
     * Holds information on whether the validation process was a success or not, will
     * be true if successful and false if unsuccessful.
     *
     * The validation process will determine when a configuration is valid, for
     * instance if a stricter validation is run it will set it as invalid even
     * if it contains warnings.
     */
    public $isValid = true;

    /**
     * A count on how many error items the validation process resulted in. The error
     * items can be accessed by traversing $resultList.
     */
    private $errorCount = 0;

    /**
     * A count on how many warning items the validation process resulted in. The
     * warning items can be accessed by traversing $resultList.
     */
    private $warningCount = 0;

    /**
     * Contains an array with ezcConfigurationValidationItem objects which are either
     * errors or warnings detected during the validation process.
     */
    private $resultList = array();

    /**
     * The location of the configuration which was validated.
     */
    private $location = false;

    /**
     * The name of the configuration which was validated.
     */
    private $name = false;

    /**
     * Similar to $name and $location but is the full path to the file being
     * read by the reader.
     */
    private $pathName = false;

    /**
     * Constructs a validation result
     *
     * Initializes the validation result with some information on the configuration
     * file and an empty result list.
     *
     * @param string $location The main placement for the configuration as
     *               returned by the reader.
     * @param string $name The name for the configuration as returned by the
     *               reader.
     * @param string $pathName A full path to the file being read by the
     *               reader.
     */
    public function __construct( $location, $name, $pathName )
    {
        $this->location = $location;
        $this->name = $name;
        $this->pathName = $pathName;
    }

    /**
     * Appends the validation item to the result list.
     *
     * @param ezcConfigurationValidationItem $item The error or warning item
     *        which should be added to the end of the result list.
     */
    public function appendItem( ezcConfigurationValidationItem $item )
    {
        if ( $item->type == ezcConfigurationValidationItem::ERROR )
        {
            $this->errorCount++;
        }
        else
        {
            $this->warningCount++;
        }
        $this->resultList[] = $item;
    }

    /**
     * Returns a list with validation items
     *
     * @return array(ezcConfigurationValidationItem) The list with items
     */
    public function getResultList()
    {
        return $this->resultList;
    }

    /**
     * Returns the number of warnings
     *
     * @return int The number of warnings
     */
    public function getWarningCount()
    {
        return $this->warningCount;
    }

    /**
     * Returns the number of errors
     *
     * @return int The number of errors
     */
    public function getErrorCount()
    {
        return $this->errorCount;
    }
}
?>
