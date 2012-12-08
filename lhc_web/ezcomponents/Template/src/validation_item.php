<?php
/**
 * File containing the ezcTemplateValidationItem class
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */
/**
 * This class provides the result of one error item which may occur during
 * execution of a template or while validating the syntax.
 *
 * A validation item consists of a type, the path to the file with line and
 * column number and a description for the end-user and the developer.
 *
 * Currently used by ezcTemplateExecutionResult.
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateValidationItem
{

    /**
     * The validation is an error which means the template execution failed.
     * @var int
     */
    const TYPE_ERROR = 1;

    /**
     * The validation is a warning which means the template execution has some
     * problems but can continue.
     * @var int
     */
    const TYPE_WARNING = 2;

    /**
     * The type of validation problem, one of the TYPE_ERROR or TYPE_WARNING values.
     * @var int
     */
    public $type = self::TYPE_ERROR;

    /**
     * The template file the error or warning occured in.
     * @var string
     */
    public $filePath = false;

    /**
     * The line number the error or warning occured on. If this is false the location
     * is unknown.
     * Note: Line numbers start with 1.
     * @var int
     */
    public $line = false;

    /**
     * The column number the error or warning occured on. If this is false the
     * location is unknown.
     * Note: Column numbers start with 0.
     * @var int
     */
    public $column = false;

    /**
     * The description of the error or warning which can be shown to the end user.
     * Note: The description should not contain the line or column number, instead
     * set the properties.
     * @var string
     */
    public $description = '';

    /**
     * Technical description of the error or warning which can be shown to the
     * developer.
     * Note: The description should not contain the line or column number, instead
     * set the properties.
     * @var string
     */
    public $details = '';

    /**
     * Initialises the validation item with location information and description.
     *
     * @param int $type The type of item, use either TYPE_ERROR or TYPE_WARNING.
     * @param string $filePath The path of the file the error or warning occured in.
     * @param int $line The line number the error or warning occured.
     * @param int $column The column number the error or warning occured.
     * @param string $description The description of the error or warning which can
     * be shown to the end user.
     * @param string $details Technical description of the error or warning which can
     * be shown to the developer.
     */
    public function __construct( $type, $filePath, $line, $column, $description, $details )
    {
        $this->type = $type;
        $this->filePath = $filePath;
        $this->line = $line;
        $this->column = $column;
        $this->description = $description;
        $this->details = $details;
    }

}
?>
