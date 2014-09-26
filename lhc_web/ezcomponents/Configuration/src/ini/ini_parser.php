<?php
/**
 * File containing the ezcConfigurationIniParser class
 *
 * @package Configuration
 * @version 1.3.5
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * This class provides functionality for parsing INI files
 *
 * @package Configuration
 * @version 1.3.5
 */
class ezcConfigurationIniParser implements Iterator
{
    /**
     * A constant to mark this parser as a reader
     */
    const PARSE = 1;

    /**
     * A constant to mark this parser as a validator
     */
    const VALIDATE = 2;

    /**
     * A constant to mark this parser as a strict validator
     */
    const VALIDATE_STRICT = 3;

    /**
     * A regexp that matches the rules that are defined for an ID
     */
    const ID_REGEXP = '([A-Za-z0-9_.-]+)';

    /**
     * A regexp that matches the rules that are defined for an dimension extension
     */
    const DIM_REGEXP = '((\[[^\]]*\])*)';

    /**
     * Contains the file pointer to the INI file that is being parsed
     */
    private $fp;

    /**
     * The path of the INI file to be parsed
     */
    private $path;

    /**
     * The last parsed item
     */
    private $item = null;

    /**
     * The current line number
     */
    private $lineNr = null;

    /**
     * Stores the current parser's type (PARSE, VALIDATE, VALIDATE_STRICT)
     */
    private $parserType = null;

    /**
     * Stores the last parsed INI settings group's name
     */
    private $currentGroup = null;

    /**
     * Stores the last comments the parser found
     */
    private $currentComments = array();

    /**
     * Stores whether an item has been found on the last parser run
     */
    private $itemFound = false;

    /**
     * Constructs the parser object
     *
     * Constructs the parser and initializes it with the file to read. After
     * construction use it as an iterator with foreach () to iterate over the
     * settings in the file.
     *
     * @param int $type What type of parser it is (PARSE, VALIDATE, VALIDATE_STRICT)
     * @param string $path The relative or absolute path to where the configuration
     * should be read from. Using PHP streams is also possible, e.g.
     * compress.gz://site.ini.gz
     */
    public function __construct( $type, $path )
    {
        $this->fp = @fopen( $path, 'rt' );
        if ( !$this->fp )
        {
            throw new ezcBaseFileNotFoundException( $path );
        }
        $this->parserType = $type;
        $this->path = $path;
        $this->lineNr = 0;
        $this->parseNext();
    }

    /**
     * Creates an new ezcConfigurationValidationItem to be returned by the current() method.
     *
     * @param string $message The error message
     */
    private function raiseError( $message )
    {
        $this->item = new ezcConfigurationValidationItem( ezcConfigurationValidationItem::ERROR, $this->path, $this->lineNr, false, $message, $message );
    }

    /**
     * Creates a group header INI item to be returned by the current() method.
     *
     * The return value is used to tell the calling function to determine
     * whether to signal that a new item is available or not.
     *
     * @param string $group The group's name
     * @param string $comments The comments belonging to this group
     *
     * @return bool Whether an item was created or not.
     */
    private function emitGroupHeader( $group, $comments )
    {
        if ( $this->parserType == ezcConfigurationIniParser::PARSE )
        {
            $this->item = new ezcConfigurationIniItem( ezcConfigurationIniItem::GROUP_HEADER, $group, '#', null, $comments, null );
            return true;
        }
        return false;
    }

    /**
     * Parses a raw INI setting's value
     *
     * This method parses the raw string value $value and converts it to a
     * proper variable type.
     *
     * @param string $value The raw value
     * @return mixed The parsed value
     */
    private static function parseIniValue( $value )
    {
        /* Check for booleans */
        if ( $value == 'false' )
        {
            return false;
        }
        if ( $value == 'true' )
        {
            return true;
        }

        /* Check for numbers - decimal */
        if ( preg_match( '@^-?(([1-9][0-9]*)|(0))$@', $value ) )
        {
            /* Check the integer range, if it falls out of the range, return a string instead. */
            if ( (string) $value === (string)(int) $value )
            {
                return (int) $value;
            }
            else
            {
                return (string) $value;
            }
        }

        /* Check for hexadecimals */
        if ( preg_match( '@^0x([0-9a-f]+)$@i', $value, $match ) )
        {
            return hexdec( $match[1] );
        }

        /* Check for octals */
        if ( preg_match( '@^0([0-7]+)$@i', $value, $match ) )
        {
            return octdec( $match[1] );
        }

        /* Check for floats */
        if ( preg_match( '@^(([0-9]*\.[0-9]+)|([0-9]+(\.[0-9]*)?))(e[+-]?[0-9]+)?$@i', $value, $match ) )
        {
            return (float) $value;
        }

        /* Check for quoted string */
        if ( strlen( $value ) && $value[0] == '"' && $value[strlen( $value ) - 1] == '"' )
        {
            $value = substr( $value, 1, -1 );
            return stripslashes( $value );
        }

        return $value;
    }

    /**
     * Creates a INI setting item to be returned by the current() method.
     *
     * The return value is used to tell the calling function to determine
     * whether to signal that a new item is available or not.
     *
     * @param string $group The group this setting belongs in
     * @param string $setting The name of the setting
     * @param string $dimensions The dimension suffix for the setting name
     * @param string $comments The comments belonging to this group
     * @param mixed $value The setting's value
     *
     * @return bool Whether an item was created or not.
     */
    private function emitSetting( $group, $setting, $dimensions, $comments, $value )
    {
        if ( $this->parserType == ezcConfigurationIniParser::PARSE )
        {
            if ( !is_null( $value ) )
            {
                $value = self::parseIniValue( $value );
            }

            /* If we have dimensions, we need to modify the array a bit so that
             * it is valid PHP code, as we're going to use evil eval() here. */
            $dimensionString = '';
            if ( strlen( $dimensions ) > 0 )
            {
                $dimensions = preg_split( '@\]\[@', substr( $dimensions, 1, -1 ) );
                foreach ( $dimensions as $dimension )
                {
                    if ( strlen( $dimension ) == 0 )
                    {
                        $dimensionString .= '[]';
                    }
                    else
                    {
                        if ( $dimension[0] == '"' && $dimension[strlen( $dimension ) - 1] == '"' )
                        {
                            $dimension = substr( $dimension, 1, -1 );
                            /* Ugly hack to convert '\n' to "\n" */
                            $dimension = eval( "return \"$dimension\";" );
                        }
                        $dimension = addslashes( $dimension );
                        $dimensionString .= "['$dimension']";
                    }
                }
            }

            $this->item = new ezcConfigurationIniItem( ezcConfigurationIniItem::SETTING, $group, $setting, $dimensionString, $comments, $value );
            return true;
        }
        return false;
    }

    /**
     * Returns the last parsed item
     *
     * Returns the last parsed item, as parsed by the next() method. Depending
     * on the type of parser (selectable in the constructor) it returns either
     * an configuration item or an configuration warning item.
     *
     * @return ezcConfigurationIniItem or ezcConfigurationValidationItem
     *          depending on the type of parser
     */
    public function current()
    {
        return $this->item;
    }

    /**
     * Returns the "key" for each element.
     *
     * This is used by the Iterator to assign a key to each "array" element. As
     * we don't use that we simply return 0.
     *
     * @return int
     */
    public function key()
    {
        return 0;
    }

    /**
     * Returns the collected comments.
     *
     * This function returns the collected comments that were stored with the
     * storeComment() method. It's called when a group header or setting is
     * found while parsing.
     *
     * @return string A string containing the comments or "null" when there is
     *                 no stored comment.
     */
    private function fetchComments()
    {
        if ( count( $this->currentComments ) )
        {
            $comments = join( "\n", $this->currentComments );
            $this->currentComments = array();
            return $comments;
        }
        return null;
    }

    /**
     * Stores a the parsed $commentLine into the currentComments array
     *
     * @param string $commentLine
     */
    private function storeComment( $commentLine )
    {
        $this->currentComments[] = $commentLine;
    }

    /**
     * Parses until the next found group header or setting name
     *
     * This method reads from the file until a header or setting name was
     * found. It checks for the four different elements that are allowed: Group
     * Header, Setting, Comment and Whitespace. If other data is found it will
     * store an validation element and abort/continue depending on which type
     * of parser is being used.
     */
    private function parseNext()
    {
        $commentArray = array();

        do
        {
            $this->itemFound = false;
            $line = trim( fgets( $this->fp ) );
            $this->lineNr++;

            /*** HEADER DETECTION *******************************************/
            if ( preg_match( '@^\[([^\]]+)\]@', $line, $matches ) )
            {
                /* We found a new group header */
                $groupID = trim( $matches[1] );
                /* Check if it is a valid ID */
                if ( preg_match( '@^'. self::ID_REGEXP . '$@', $groupID ) )
                {
                    $this->currentGroup = $groupID;
                    $headerComments = $this->fetchComments();

                    if ( $this->emitGroupHeader( $this->currentGroup, $headerComments ) )
                    {
                        $this->itemFound = true;
                        return;
                    }
                }
                else
                {
                    $this->raiseError( "Group ID '{$groupID}' has invalid characters" );
                    return;
                }
            }

            /*** COMMENT DETECTION ******************************************/
            else if ( preg_match( '@^[#;](.*)@', $line, $matches ) )
            {
                /* We found a comment, process it */
                $this->storeComment( $matches[1] );
            }

            /*** SETTING DETECTION ******************************************/
            else if ( preg_match( '@^('. self::ID_REGEXP . self::DIM_REGEXP . ')\s*=\s*(.*)@', $line, $matches ) )
            {
                $settingID = $matches[2];
                $settingDimensions = $matches[3];
                $settingValue = $matches[5];

                /* There could be a check if the setting name is not valid ID,
                 * but that is unnecesary because the previous regexps
                 * filter this out already.:
                if ( !preg_match( '@'. self::ID_REGEXP . '@', $settingID ) )
                {
                    $this->raiseError( "Setting ID '{$settingID}' has invalid characters" );
                    return;
                } */

                $settingComments = $this->fetchComments();
                if ( $this->emitSetting( $this->currentGroup, $settingID, $settingDimensions, $settingComments, $settingValue ) )
                {
                    $this->itemFound = true;
                    return;
                }
            }

            /*** WHITESPACE DETECTION ***************************************/
            else if ( preg_match( '@^\s*$@', $line, $matches ) )
            {
                /* do nothing with whitespace */
            }

            /*** CATCH ALL *************************************************/
            else
            {
                $this->raiseError( "Invalid data: '{$line}'" );
                return;
            }
        }
        while ( !feof( $this->fp ) );
    }

    /**
     * Advances until the next parser element has been found
     *
     * This is used by the Iterator to advance to the next element. It calls
     * the parseNext() method which returns when a Group Header, Setting or
     * Validation error has been found.
     */
    public function next()
    {
        $this->parseNext();
    }

    /**
     * Throws an Exception saying that this should not be used.
     *
     * This is used by the Iterator to rewind to the start of the array. As
     * this implementation of the Iterator interface does not allow this, and
     * can only be used together with the NoRewindIterator mechanism it simply
     * throws an Exception.
     *
     * @throws Exception whenever this method is called.
     */
    public function rewind()
    {
        throw new Exception( 'You can only use this implementation of the iterator with a NoRewindIterator.' );
    }

    /**
     * Returns whether there are more valid elements in the file.
     *
     * This is used by the Iterator to check whether the end of the array has
     * been reached. We simply check for the end-of-file marker as "valid
     * condition".
     *
     * @return bool Whether the end of file has been reached.
     */
    public function valid()
    {
        return !feof( $this->fp ) || $this->itemFound;
    }
}
?>
