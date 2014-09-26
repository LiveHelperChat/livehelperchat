<?php
/**
 * File containing the ezcDebug class.
 *
 * @package Debug
 * @version 1.2.1
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Base iterator class to wrap stack traces.
 *
 * This class provides a basis for stack trace iterators that are stored for
 * each call to {@link ezcDebug::log()} if {@link ezcDebugOptions::$stackTrace}
 * is switched on or the specific parameter is set. The stack trace iterator
 * needs to ensure, that the created stack trace is converted to the format
 * understandable by the {@link ezcDebugFormatter} as defined in {@link
 * ezcDebugStacktraceIterator::unifyStackElement()}.
 * 
 * @package Debug
 * @version 1.2.1
 */
abstract class ezcDebugStacktraceIterator implements Iterator, ArrayAccess, Countable
{

    /**
     * Raw stack trace as an array.
     * 
     * @var array
     */
    private $stackTrace;

    /**
     * Options. 
     * 
     * @var ezcDebugOptions
     */
    protected $options;

    /**
     * Creates a new stack trace iterator.
     *
     * Calls {@link ezcDebugStacktraceIterator::prepare()} internally to
     * prepare the stack trace before storing it.
     * 
     * @param mixed $stackTrace 
     * @param int $removeElements
     * @param ezcDebugOptions $options
     * @return void
     */
    public final function __construct( $stackTrace, $removeElements = 2, ezcDebugOptions $options )
    {
        $this->options    = $options;
        $this->stackTrace = $this->prepare( $stackTrace, $removeElements );
    }

    /**
     * Unifies a stack element for being returned to the formatter.
     *
     * This method ensures that an element of the stack trace conforms to the
     * format expected by a {@link ezcDebugOutputFormatter}. The format is
     * defined as follows:
     *
     * <code>
     * array(
     *      'file'      => '<fullpathtofile>',
     *      'line'      => <lineno>,
     *      'function'  => '<functionname>',
     *      'class'     => '<classname>',
     *      'params'    => array(
     *          <param_no> => '<paramvalueinfo>',
     *          <param_no> => '<paramvalueinfo>',
     *          <param_no> => '<paramvalueinfo>',
     *          ...
     *      )
     * )
     * </code>
     * 
     * @param mixed $stackElement 
     * @return array As described above.
     */
    protected abstract function unifyStackElement( $stackElement );

    /**
     * Prepares the stack trace for being stored in the iterator instance.
     *
     * This method is called by {@link
     * ezcDebugStacktraceIterator::__construct()} before the stack trace is
     * stored in the corresponding property. The given array can be manipulated
     * as needed to prepare the trace and the array to store internally must be
     * returned. The basic implementation removes $removeElements number of
     * elements from the start of the trace array and reduces the array to
     * {@link ezcDebugOptions::$stackTraceDepth} elements.
     *
     * @param array $stackTrace 
     * @param int $removeElements 
     */
    protected function prepare( $stackTrace, $removeElements )
    {
        return array_slice(
            $stackTrace,
            $removeElements,
            ( ( $elementCount = $this->options->stackTraceDepth ) === 0 ? null : $elementCount )
        );
    }

    /**
     * Returns the currently selected element of the iterator.
     *
     * This method is part of the Iterator interface.
     *
     * @return mixed
     */
    public final function current()
    {
        return $this->unifyStackElement(
            current( $this->stackTrace )
        );
    }

    /**
     * Returns the key of the currently selected element of the iterator.
     *
     * This method is part of the Iterator interface.
     *
     * @return mixed
     */
    public final function key()
    {
        return key( $this->stackTrace );
    }

    /**
     * Advances the iterator to the next element. 
     *
     * This method is part of the Iterator interface.
     * 
     * @return mixed
     */
    public final function next()
    {
        return next( $this->stackTrace );
    }

    /**
     * Resets the iterator to the first element.
     *
     * This method is part of the Iterator interface.
     * 
     * @return mixed
     */
    public final function rewind()
    {
        return reset( $this->stackTrace );
    }

    /**
     * Returns if the iterator is on a valid element or at the end.
     *
     * This method is part of the Iterator interface.
     * 
     * @return bool
     */
    public final function valid()
    {
        return ( current( $this->stackTrace ) !== false );
    }

    /**
     * Returns if the given offset exists.
     *
     * This method is part of the ArrayAccess interface.
     * 
     * @param mixed $offset 
     * @return bool
     */
    public final function offsetExists( $offset )
    {
        return array_key_exists( $offset, $this->stackTrace );
    }
    
    /**
     * Returns the value assigned to the given offset. 
     *
     * This method is part of the ArrayAccess interface.
     * 
     * @param mixed $offset 
     * @return mixed
     */
    public final function offsetGet( $offset )
    {
        if ( !$this->offsetExists( $offset ) )
        {
            throw new ezcBaseValueException(
                'offset',
                $offset,
                'valid offset'
            );
        }
        return $this->unifyStackElement( $this->stackTrace[$offset] );
    }

    /**
     * It is not allowed to use this method with this iterator.
     *
     * This method is part of the ArrayAccess interface.
     *
     * @throws ezcDebugException
     * @param mixed $offset 
     * @param mixed $value 
     * @return void
     */
    public final function offsetSet( $offset, $value )
    {
        throw new ezcDebugOperationNotPermittedException(
            'setting values via ArrayAccess'
        );
    }

    /**
     * It is not allowed to use this method with this iterator.
     *
     * This method is part of the ArrayAccess interface.
     *
     * @throws ezcDebugException
     * @param mixed $offset 
     * @return void
     */
    public final function offsetUnset( $offset )
    {
        throw new ezcDebugOperationNotPermittedException(
            'unsetting values via ArrayAccess'
        );
    }

    /**
     * Returns the number of elements in the iterator.
     *
     * This method is part of the Countable interface.
     * 
     * @return int
     */
    public final function count()
    {
        return count( $this->stackTrace );
    }
}

?>
