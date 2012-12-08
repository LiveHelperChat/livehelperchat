<?php
/**
 * File containing the ezcWebdavLockIfHeaderCondition struct class.
 *
 * @package Webdav
 * @version 1.1.4
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 *
 * @access private
 */
/**
 * Struct class representing an single condition element in an If header.
 *
 * The If header consists of different list types {@link
 * ezcWebdavIfHeaderList}, which contain items that represent condition sets. A
 * condition is represented by an instance of this class. Conditions are either
 * lock tokens or ETags.
 * 
 * @package Webdav
 * @version 1.1.4
 *
 * @access private
 */
class ezcWebdavLockIfHeaderCondition
{
    /**
     * Content of the condition. 
     * 
     * @var string
     */
    public $content;

    /**
     * If this condition is negated. 
     * 
     * @var bool
     */
    public $negated = false;

    /**
     * Creates a new If header condition.
     * 
     * @param string $content 
     * @param bool $negated 
     */
    public function __construct( $content, $negated = false )
    {
        $this->content = $content;
        $this->negated = $negated;
    }

    /**
     * Returns the string representation of this condition.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }
}

?>
