<?php
/**
 * File containing the ezcDocumentRomanListItemGenerator class.
 *
 * @package Document
 * @version 1.3.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * Roman number list item generator.
 *
 * Generator for roman numbered list items. Basically converts the list item 
 * number into a roman number and returns that. Roman numbering is only 
 * properly support up to numbers of about 1000. Lists with more items will 
 * generate strange to read numbers, because they can only be represented using 
 * lots of repetitions of the "M" representing 1000.
 *
 * @package Document
 * @access private
 * @version 1.3.1
 */
class ezcDocumentRomanListItemGenerator extends ezcDocumentAlnumListItemGenerator
{
    /**
     * Number map.
     * 
     * @var array(int=>string)
     */
    protected $numbers = array(
        1000 => 'M',
        900  => 'CM',
        500  => 'D',
        400  => 'CD',
        100  => 'C',
        90   => 'XC',
        50   => 'L',
        40   => 'XL',
        10   => 'X',
        9    => 'IX',
        5    => 'V',
        4    => 'IV',
        1    => 'I',
    );

    /**
     * Get list item.
     *
     * Get the n-th list item. The index of the list item is specified by the
     * number parameter.
     * 
     * @param int $number 
     * @return string
     */
    public function getListItem( $number )
    {
        $item = '';
        foreach ( $this->numbers as $value => $char )
        {
            while ( $number >= $value )
            {
                $item   .= $char;
                $number -= $value;
            }
        }

        return $this->applyStyle( $item );
    }
}

?>
