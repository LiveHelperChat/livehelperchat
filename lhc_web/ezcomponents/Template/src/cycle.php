<?php
/**
 * File containing the ezcTemplateCycle class.
 *
 * @package Template
 * @version 1.4.2
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @access private
 */

/**
 * This class is the implementation of the cycle variable inside a template.
 * At the declaration of a new cycle variable, a new ezcTemplateCycle object
 * is created. Everywhere the cycle is used furthermore, it calls implicitly the
 * __set() and __get() methods via any property-call (usually ->v).
 *
 * @package Template
 * @version 1.4.2
 * @access private
 */
class ezcTemplateCycle
{
    /**
     * The value that the cycle currently contains.
     *
     * @var array(mixed)|false
     */
    private $value = false;

    /**
     * An internal pointer to the current element in the cycle array: $value.
     *
     * @var int
     */
    private $counter = 0;

    /**
     * Keeps track of the size of the cycle array: $value.
     *
     * @var int $size
     */
    private $size;


    public function __construct()
    {
    }

    /**
     * For any property name it will set the ezcTemplateCycle::value to the 
     * given $value.
     *
     * @param string $name         Value will be ignored.
     * @param array(mixed) $value  If not an array is assigned, it will set the value to false.
     * @return void
     */
    public function __set( $name, $value )
    {
        if ( is_array( $value ) )
        {
            $this->value = $value;
            $this->size = sizeof( $value );

            if ( $this->size > 0 ) return;
        }

        $this->value = false;
    }

    /**
     * Return the current array element it is pointing to. The property 
     * name $name is ignored.
     *
     * @param string $name  Value will be ignored.
     * @return mixed        Returns the current array element it is pointing to.
     */
    public function __get( $name )
    {
        if ( $this->value !== false )
        {
            $res = $this->value[ $this->counter ];

            return $res;
        }

        throw new ezcTemplateInternalException("Invalid cycle: " . $name );
    }

    /**
     * Increment the internal array element pointer. If it goes outside the array
     * boundaries, the pointer will be set to the first element. 
     *
     * @return void
     */
    public function increment()
    {
        $this->counter = ++$this->counter % $this->size;
    }

    /**
     * Increment the internal array element pointer. If it goes outside the array
     * boundaries, the pointer will be set to the last element. 
     *
     * @return void
     */
    public function decrement()
    {
        if ( --$this->counter  < 0 ) $this->counter = $this->size - 1;
    }

    /**
     * Set the internal pointer to the first array element.
     * @return void
     *
     */
    public function reset()
    {
        $this->counter = 0;
    }
}


?>
