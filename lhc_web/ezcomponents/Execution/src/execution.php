<?php
/**
 * @copyright Copyright (C) 2005-2008 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.1.1
 * @filesource
 * @package Execution
 */

/**
 * Class for handling fatal errors
 *
 * This class allows you to invoke a handler whenever a fatal error occurs, or
 * when an uncatched exception happens.  You can specify which callback
 * function to use and signal whether your application cleanly exited.
 *
 * Example:
 * <code>
 * <?php
 * class myExecutionHandler extends ezcExecutionBasicErrorHandler
 * {
 *     public static function onError( Exception $exception = null )
 *     {
 *         echo "Error!\n";
 *         // If you want, you can use the parent's onError method, but that
 *         // will only show a standard message.
 *         parent::onError( $exception );
 *     }
 * }
 * 
 * ezcExecution::init( 'myExecutionHandler' );
 * 
 * // ....
 * 
 * ezcExecution::cleanExit();
 * ?>
 * </code>
 *
 * @package Execution
 * @version 1.1.1
 * @mainclass
 */
class ezcExecution
{
    /**
     * Used for keeping a record whether the ezcExecution mechanism was
     * initialized through the init() method.
     * @var bool
     */
    static private $isInitialized = false;

    /**
     * Contains the name of the class that is going to be used to call the
     * onError() static method on when an fatal error situation occurs.
     * @var string
     */
    static private $callbackClassName = null;

    /**
     * Records whether we had a clean exit from the application or not. If it's
     * false then the shutdownCallbackHandler() method will not call the
     * Execution Handler's onError() method.
     * @var bool
     */
    static private $cleanExit = false;

    /**
     * A shutdown handler can not be removed, and that's why this static
     * property records if it was set or not. This ensures that the class will
     * only set the shutdown handler once over the lifetime of a request.
     * @var bool
     */
    static private $shutdownHandlerRegistered = false;

    /**
     * Initializes the ezcExecution environment.
     *
     * With this method you initialize the ezcExecution environment. You need
     * to specify a class name $callBackClassName that will be used to call the
     * onError() method on in case of an error. The class name that you
     * pass should implement the ezcExecutionErrorHandler interface. This
     * method takes care of registering the uncaught exception and shutdown
     * handlers. 
     *
     * @throws ezcExecutionInvalidCallbackException if an unknown callback
     *         class was passed.
     * @throws ezcExecutionWrongClassException if an invalid callback class was
     *         passed.
     * @throws ezcExecutionAlreadyInitializedException if the environment was
     *         already initialized.
     *
     * @param string $callbackClassName 
     * @return void
     */
    static public function init( $callbackClassName )
    {
        // Check if the passed classname actually exists
        if ( !ezcBaseFeatures::classExists( $callbackClassName, true ) )
        {
            throw new ezcExecutionInvalidCallbackException( $callbackClassName );
        }

        // Check if the passed classname actually implements the interface.
        if ( !in_array( 'ezcExecutionErrorHandler', class_implements( $callbackClassName ) ) )
        {
            throw new ezcExecutionWrongClassException( $callbackClassName );
        }

        // Check if it was already initialized once
        if ( self::$isInitialized == true )
        {
            throw new ezcExecutionAlreadyInitializedException();
        }

        // Install shutdown handler and exception handler
        set_exception_handler( array( 'ezcExecution', 'exceptionCallbackHandler' ) );
        if ( !self::$shutdownHandlerRegistered )
        {
            register_shutdown_function( array( 'ezcExecution', 'shutdownCallbackHandler' ) );
        }
        self::$callbackClassName = $callbackClassName;
        self::$isInitialized = true;
        self::$cleanExit = false;
        self::$shutdownHandlerRegistered = true;
    }

    /**
     * Resets the ezcExecution environment.
     *
     * With this function you reset the environment. Usually this method should
     * not be used, but in the cases when you want to restart the environment
     * this is the method to use. It also takes care of restoring the user
     * defined uncaught exception handler, but it can not undo the registered
     * shutdown handler as PHP doesn't provide that functionality.
     *
     * @return void
     */
    static public function reset()
    {
        if ( self::$isInitialized )
        {
            restore_exception_handler();
        }
        self::$callbackClassName = null;
        self::$isInitialized = false;
        self::$cleanExit = false;
    }

    /**
     * Marks the current application as cleanly-exited.
     *
     * With this method you signal the ezcExecution environment that your
     * application ended without errors. This is usually done just before you
     * reach the end of your script, or just before you call 
     * {@link http://www.php.net/exit exit()} or 
     * {@link http://www.php.net/die die()}.
     *
     * @throws ezcExecutionNotInitializedException if the environment was not
     *         yet initialized.
     * @return void
     */
    static public function cleanExit()
    {
        self::$cleanExit = true;        
    }

    /**
     * Handler for uncaught exceptions.
     *
     * The ezcExecution environment installs this method as handler for
     * uncaught exceptions and will be called when one is found.  This method's
     * only function is to call the ::onError() static method of the error
     * handler set with the init() method. It passes along the uncaught
     * exception to this method.
     *
     * This method has to be public otherwise PHP can not call it, but you
     * should never call this method yourself.
     *
     * @param Exception $e
     * @return void
     */
    static public function exceptionCallbackHandler( Exception $e )
    {
        self::$cleanExit = true;        
        call_user_func( array( self::$callbackClassName, 'onError' ), $e );
    }

    /**
     * Shutdown handler
     *
     * The ezcExecution environment installs this method as general shutdown handler.
     * This method's only function is to call the ::onError() static method of
     * the error handler set with the init() method.
     *
     * @return void
     */
    static public function shutdownCallbackHandler()
    {
        if ( !self::$cleanExit )
        {
            self::$cleanExit = true;        
            call_user_func( array( self::$callbackClassName, 'onError' ) );
        }
    }
}
?>
