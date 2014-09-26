<?php
/**
 * File containing the ezcConsoleQuestionDialog class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Dialog class to ask a simple question.
 * This dialog outputs a certain string to the user and requests a line of
 * input. This is commonly used to ask questions like "Do you want to proceed?"
 * and retrieve an answer like "y" for yes or "n" for no.
 *
 * The behaviour of this dialog is defined by an instance of
 * {@link ezcConsoleQuestionDialogOptions}.
 * 
 * @package ConsoleTools
 * @version 1.6.1
 *
 * @property ezcConsoleQuestionDialogOptions $options
 *           Options for the dialog.
 * @property ezcConsoleOutput $output
 *           Output object for displaying the dialog.
 */
class ezcConsoleQuestionDialog implements ezcConsoleDialog
{

    /**
     * Dialog result 
     * 
     * @var mixed
     */
    protected $result;

    /**
     * Properties 
     * 
     * @var array
     */
    protected $properties = array(
        "options"   => null,
        "output"    => null,
    );

    /**
     * Creates a new question dialog.
     * Creates a new menu dialog to be displayed to the user. All behaviour is
     * defined through the $options parameter. The $output parameter is used to
     * display the dialog in the {@link display()} method.
     * 
     * @param ezcConsoleOutput $output                 Output object.
     * @param ezcConsoleQuestionDialogOptions $options Options.
     * @return void
     */
    public function __construct( ezcConsoleOutput $output, ezcConsoleQuestionDialogOptions $options = null )
    {
        $this->output  = $output;
        $this->options = $options === null ? new ezcConsoleQuestionDialogOptions() : $options;
    }

    /**
     * Returns if the dialog retrieved a valid result.
     * If a valid result has already been received, this method returns true,
     * otherwise false.
     * 
     * @return bool If a valid result was retrieved.
     */
    public function hasValidResult()
    {
        return $this->result !== null;
    }

    /**
     * Returns the result retrieved.
     * If no valid result was retreived, yet, this method should throw an
     * ezcConsoleNoValidDialogResultException.
     * 
     * If no valid result was retreived, yet, this method throws an
     * ezcConsoleNoValidDialogResultException. Use {@link hasValidResult()} to
     * avoid this.
     * 
     * @return mixed The retreived result.
     *
     * @throws ezcDialogNoValidResultException
     *         if this method is called without a valid result being retrieved
     *         by the object. Use {@link hasValidResult()} to avoid this
     *         exception.
     */
    public function getResult()
    {
        if ( $this->result === null )
        {
            throw new ezcConsoleNoValidDialogResultException();
        }
        return $this->result;
    }

    /**
     * Displays the dialog and retreives a value from the user.
     * Displays the dialog and retreives the desired answer from the user. If
     * the a valid result is retrieved, it can be obtained using {@link
     * getResult()}. The method {@link hasValidResult()} can be used to check
     * if a valid result is available.
     * 
     * @return void
     * @throws ezcConsoleDialogAbortException
     *         if the user closes STDIN using <CTRL>-D.
     */
    public function display()
    {
        $this->reset();

        $this->output->outputText(
            $this->options->text . ( $this->options->showResults === true ? " " . $this->options->validator->getResultString() : "" ) . " ",
            $this->options->format
        );
        $result = $this->options->validator->fixup( ezcConsoleDialogViewer::readLine() );
        if ( $this->options->validator->validate( $result ) )
        {
            $this->result = $result;
        }
    }

    /**
     * Reset the dialog.
     * Resets a possibly received result and all changes made to the dialog
     * during {@link display()}. After that, the dialog can be re-used. All
     * option values are kept.
     * 
     * @return void
     */
    public function reset()
    {
        $this->result = null;
    }

    /**
     * Returns a ready to use yes/no question dialog.
     * Returns a question dialog, which requests the answers "y" for "yes" or
     * "n" for "no" from the user. The answer is converted to lower-case.
     *
     * <code>
     * // Would you like to proceed? (y/n) 
     * $dialog = ezcConsoleDialog( $out, "Would you like to proceed?" );
     *
     * // Would you like to proceed? (y/n) [n] 
     * $dialog = ezcConsoleDialog( $out, "Would you like to proceed?", "n" );
     * </code>
     * 
     * @param ezcConsoleOutput $out  Output object.
     * @param string $questionString Question string.
     * @param string $default        "y" or "n", if default value is desired.
     * @return ezcConsoleQuestionDialog The created dialog.
     */
    public static function YesNoQuestion( ezcConsoleOutput $out, $questionString, $default = null )
    {
        $opts = new ezcConsoleQuestionDialogOptions();
        $opts->text = $questionString;
        $opts->showResults = true;
        $opts->validator = new ezcConsoleQuestionDialogMappingValidator(
            array( "y", "n" ),
            $default,
            ezcConsoleQuestionDialogCollectionValidator::CONVERT_LOWER,
            array(
                'yes' => 'y',
                'no'  => 'n',
            )
        );

        return new ezcConsoleQuestionDialog( $out, $opts );
    }

    /**
     * Property read access.
     *
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * 
     * @param string $propertyName Name of the property.
     * @return mixed Value of the property or null.
     * @ignore
     */
    public function __get( $propertyName )
    {
        if ( array_key_exists( $propertyName, $this->properties ) )
        {
            return $this->properties[$propertyName];
        }
        throw new ezcBasePropertyNotFoundException( $propertyName );
    }

    /**
     * Property write access.
     * 
     * @param string $propertyName Name of the property.
     * @param mixed $propertyValue The value for the property.
     *
     * @throws ezcBasePropertyPermissionException
     *         If the property you try to access is read-only.
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * @ignore
     */
    public function __set( $propertyName, $propertyValue )
    {
        switch ( $propertyName )
        {
            case "options":
                if ( ( $propertyValue instanceof ezcConsoleQuestionDialogOptions ) === false )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        ( is_object( $propertyValue ) ? get_class( $propertyValue ) : gettype( $propertyValue ) ),
                        "instance of ezcConsoleQuestionDialogOptions"
                    );
                }
                break;
            case "output":
                if ( ( $propertyValue instanceof ezcConsoleOutput ) === false )
                {
                    throw new ezcBaseValueException(
                        $propertyName,
                        ( is_object( $propertyValue ) ? get_class( $propertyValue ) : gettype( $propertyValue ) ),
                        "instance of ezcConsoleOutput"
                    );
                }
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $propertyName );
        }
        $this->properties[$propertyName] = $propertyValue;
    }

    /**
     * Property isset access.
     * 
     * @param string $propertyName Name of the property to check.
     * @return bool If the property exists or not.
     * @ignore
     */
    public function __isset( $propertyName )
    {
        return array_key_exists( $propertyName, $this->properties );
    }
}

?>
