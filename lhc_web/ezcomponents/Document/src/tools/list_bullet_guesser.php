<?php
/**
 * File containing the ezcDocumentListBulletGuesser class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Simple mapping class to guess bullet charachters from mark names.
 *
 * @package Document
 * @version 1.3.1
 * @access private
 */
class ezcDocumentListBulletGuesser
{
    /**
     * Mapping of mark names to UTF-8 bullet characters.
     * 
     * @var array(string=>string)
     */
    protected $docBookCharMap = array(
        'circle' => '⚪',
        'circ'   => '⚪',
        'square' => '◼',
        'dics'   => '⚫',
        'skull'  => '☠',
        'smiley' => '☺',
        'arrow'  => '→',
    );

    /**
     * Returns a UTF-8 bullet character for the given $mark.
     *
     * $mark can be a single character, in which case this character is 
     * returned. Otherwise, the given $mark string is tried to be interpreted 
     * and an according UTF-8 char is returned, if found. If this match fails, 
     * the $default is returned.
     * 
     * @param string $mark 
     * @param string $default 
     * @return string
     */
    public function markToChar( $mark, $default = '⚫' )
    {
        if ( iconv_strlen( $mark, 'UTF-8' ) === 1 )
        {
            return $mark;
        }
        $mark = strtolower( $mark );
        if ( isset( $this->docBookCharMap[$mark] ) )
        {
            return $this->docBookCharMap[$mark];
        }
        return $default;
    }
}

?>
