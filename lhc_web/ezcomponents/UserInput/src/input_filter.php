<?php
/**
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.4
 * @filesource
 * @package UserInput
 */

/**
 * Provides a set of standard filters.
 *
 * This class defines a set of filters that can be used with both with PHP's
 * filter extension, or with the ezcInputForm class as callback filter method.
 *
 * <code>
 * <?php
 * $definition = array(
 *    'special' => array( OPTIONAL, 'callback',
 *                                  array( 'ezcInputFilter', 'urlFilter' ) ),
 * );
 * $form = new ezcInputForm( ezcInputForm::INPUT_GET, $definition );
 * ?>
 * </code>
 *
 * @package UserInput
 * @version 1.4
 */
class ezcInputFilter
{
    /**
     * Receives a variable for filtering. The filter function is free to modify
     * the variable and should return the modified variable.
     *
     * @param mixed  $value        The variable's value
     * @param string $characterSet The value's character set
     * @return mixed The modified value of the variable that was passed
     */
    static function urlFilter( $value, $characterSet )
    {
    }
}
?>
