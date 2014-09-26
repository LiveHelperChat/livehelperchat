<?php
/**
 * File containing the ezcConsoleArguments collection class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Collection class for ezcConsoleArgument objects. Used in {@link ezcConsoleInput}.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleArguments implements ArrayAccess, Iterator, Countable
{
    /**
     * Ordered list of arguments. 
     * 
     * @var array(ezcConsoleArgument)
     */
    protected $ordered = array();

    /**
     * Named list of arguments. 
     * 
     * @var array(string=>ezcConsoleArgument)
     */
    protected $named = array();

    /**
     * Returns if the given offset exists.
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. Valid offsets are integers or
     * strings. If an integer is used, it refers to the position in the command
     * line. A string refers to the arguments name property.
     * 
     * @param mixed $offset The offset to check.
     * @return bool True when the offset exists, otherwise false.
     * 
     * @throws ezcBaseValueException
     *         If the provided offset is neither an integer, nor a string.
     */
    public function offsetExists( $offset )
    {
        switch ( gettype( $offset ) )
        {
            case "string":
                return array_key_exists( $offset, $this->named );
            case "integer":
                return array_key_exists( $offset, $this->ordered );
            default:
                throw new ezcBaseValueException( "offset", $offset, "string or int" );
        }
    }

    /**
     * Returns the element with the given offset. 
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. Valid offsets are integers or
     * strings. If an integer is used, it refers to the position in the command
     * line. A string refers to the arguments name property.
     * 
     * @param string|integer $offset The offset to check.
     * @return ezcConsoleArgument
     *
     * @throws ezcBaseValueException
     *         If the provided offset is neither an integer, nor a string.
     */
    public function offsetGet( $offset )
    {
        switch ( gettype( $offset ) )
        {
            case "string":
                if ( isset( $this[$offset] ) )
                {
                    return $this->named[$offset];
                }
                break;
            case "integer":
                if ( isset( $this[$offset] ) )
                {
                    return $this->ordered[$offset];
                }
                break;
            default:
                throw new ezcBaseValueException( "offset", $offset, "string or int" );
        }
        throw new ezcBasePropertyNotFoundException( $offset );
    }

    /**
     * Set the element with the given offset. 
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. In contrast to the other
     * ArrayAccess implementations of this class, this method allows only integer
     * keys.
     * 
     * @param int $offset               The offset to assign an item to.
     * @param ezcConsoleArgument $value The argument object to register.
     * @return void
     *
     * @throws ezcBaseValueException
     *         If a non integer offset is provided.
     * @throws ezcBaseValueException
     *         If the provided value is not of type {@ling ezcConsoleTableRow}.
     * @throws ezcConsoleArgumentAlreadyRegisteredException
     *         If an argument with the given offset or name is already registered.
     */
    public function offsetSet( $offset, $value )
    {
        // Determine key if not set (using $obj[] = ...)
        if ( $offset === null )
        {
            $offset = count( $this->ordered ) === 0 ? 0 : max( array_keys( $this->ordered ) ) + 1;
        }

        // Set access only allowed with integer values
        if ( !is_int( $offset ) )
        {
            throw new ezcBaseValueException( "offset", $offset, "int" );
        }
        
        switch ( true )
        {
            case ( $value instanceof ezcConsoleArgument ):
                if ( isset( $this->ordered[$offset] ) )
                {
                    throw new ezcConsoleArgumentAlreadyRegisteredException( $offset, ezcConsoleArgumentAlreadyRegisteredException::ORDERED );
                }
                if ( isset( $this->named[$value->name] ) )
                {
                    throw new ezcConsoleArgumentAlreadyRegisteredException( $value->name, ezcConsoleArgumentAlreadyRegisteredException::NAMED );
                }

                $this->named[$value->name] = $value;
                $this->ordered[$offset]    = $value;
                break;
            case ( $value === null ):
                // Aliasing unset() with assignement to null
                unset( $this->named[$this->ordered[$offset]->name] );
                unset( $this->ordered[$offset] );
                break;
            default:
                throw new ezcBaseValueException( "value", $value, "ezcConsoleArgument or null" );
        }
    }

    /**
     * Unset the element with the given offset. 
     * This method is part of the ArrayAccess interface to allow access to the
     * data of this object as if it was an array. In contrast to the other
     * ArrayAccess implementations of this class, this method allows only integer
     * keys.
     * 
     * @param int $offset The offset to unset the value for.
     * @return void
     *
     * @throws ezcBaseValueException
     *         If a non numeric row offset is used.
     */
    public function offsetUnset( $offset )
    {
        // Set access only allowed with integer values
        if ( is_int( $offset ) === false )
        {
            throw new ezcBaseValueException( "offset", $offset, "int" );
        }

        unset( $this->named[$this->ordered[$offset]->name] );
        unset( $this->ordered[$offset] );
    }

    /**
     * Returns the currently selected argument from the list.
     * Used by foreach-Loops.
     * 
     * @return ezcConsoleArgument
     */
    public function current()
    {
        return current( $this->ordered );
    }

    /**
     * Returns the key of the currently selected argument from the list.
     * Used by foreach-Loops. In contrast to the iteration direction, which is
     * defined by the ordered list of arguments, this is the name of the
     * argument.
     * 
     * @return string
     */
    public function key()
    {
        return key( $this->ordered );
    }

    /**
     * Advances the internal pointer to the next argument and returns it. 
     * Used by foreach-Loops.
     * 
     * @return ezcConsoleArgument
     */
    public function next()
    {
        return next( $this->ordered );
    }

    /**
     * Rewinds the internal pointer to the first argument and returns it.
     * Used by foreach-Loops.
     * 
     * @return ezcConsoleArgument
     */
    public function rewind()
    {
        // Called before foreach
        ksort( $this->ordered );
        return reset( $this->ordered );
    }

    /**
     * Checks if the current position is valid.
     * Used by foreach-Loops.
     * 
     * @return bool
     */
    public function valid()
    {
        return ( current( $this->ordered ) !== false );
    }

    /**
     * Returns the number of registered arguments.
     * 
     * @return int
     */
    public function count()
    {
        return count( $this->ordered );
    }
}
?>
