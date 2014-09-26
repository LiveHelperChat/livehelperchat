<?php
/**
 * File containing the ezcConsoleOption class.
 *
 * @package ConsoleTools
 * @version 1.6.1
 * @copyright Copyright (C) 2005-2010 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @filesource
 */

/**
 * Objects of this class store data about a single option for ezcConsoleInput.
 *
 * This class represents a single command line option, which can be handled by 
 * the ezcConsoleInput class. This classes only purpose is the storage of
 * the parameter data, the handling of options and arguments is done by the
 * class {@link ezcConsoleInput}.
 * 
 * @property-read string $short
 *                Short name of the parameter without '-' (eg. 'f').
 * @property-read string $long
 *                Long name of the parameter without '--' (eg. 'file').
 * @property int $type
 *           Value type of this parameter, default is ezcConsoleInput::TYPE_NONE.
 *           See {@link ezcConsoleInput::TYPE_NONE},
 *           {@link ezcConsoleInput::TYPE_INT} and
 *           {@link ezcConsoleInput::TYPE_STRING}.
 * @property mixed $default
 *           Default value if the parameter is submitted without value.  If a
 *           parameter is eg. of type ezcConsoleInput::TYPE_STRING and
 *           therefore expects a value when being submitted, it may be
 *           submitted without a value and automatically get the default value
 *           specified here.
 * @property bool $multiple
 *           Is the submission of multiple instances of this parameters
 *           allowed? 
 * @property string $shorthelp
 *           Short help text. Usually displayed when showing parameter help
 *           overview.
 * @property string $longhelp
 *           Long help text. Usually displayed when showing parameter detailed
 *           help.
 * @property bool $arguments
 *           Whether arguments to the program are allowed, when this parameter
 *           is submitted. 
 * @property bool $mandatory
 *           Whether a parameter is mandatory to be set.  If this flag is true,
 *           the parameter must be submitted whenever the program is run.
 * @property bool $isHelpOption
 *           Whether a parameter is a help option.  If this flag is true, and
 *           the parameter is set, all options marked as mandatory may be
 *           skipped.
 *
 * @package ConsoleTools
 * @version 1.6.1
 */
class ezcConsoleOption
{
    /**
     * Container to hold the properties
     *
     * @var array(string=>mixed)
     */
    protected $properties;

    /**
     * Dependency rules of this parameter.
     * 
     * @see ezcConsoleOption::addDependency()
     * @see ezcConsoleOption::removeDependency()
     * @see ezcConsoleOption::hasDependency()
     * @see ezcConsoleOption::getDependencies()
     * @see ezcConsoleOption::resetDependencies()
     * 
     * @var array(string=>ezcConsoleParamemterRule)
     */
    protected $dependencies = array();

    /**
     * Exclusion rules of this parameter.
     * 
     * @see ezcConsoleOption::addExclusion()
     * @see ezcConsoleOption::removeExclusion()
     * @see ezcConsoleOption::hasExclusion()
     * @see ezcConsoleOption::getExclusions()
     * @see ezcConsoleOption::resetExclusions()
     * 
     * @var array(string=>ezcConsoleParamemterRule)
     */
    protected $exclusions = array();

    /**
     * The value the parameter was assigned to when being submitted.
     * Boolean false indicates the parameter was not submitted, boolean
     * true means the parameter was submitted, but did not have a value.
     * In any other case, this caries the submitted value.
     * 
     * @var mixed
     */
    public $value = false;

    /**
     * Create a new parameter struct.
     * Creates a new basic parameter struct with the base information "$short"
     * (the short name of the parameter) and "$long" (the long version). You
     * simply apply these parameters as strings (without '-' or '--'). So
     *
     * <code>
     * $param = new ezcConsoleOption( 'f', 'file' );
     * </code>
     *
     * will result in a parameter that can be accessed using
     * 
     * <code>
     * $ mytool -f
     * </code>
     *
     * or
     * 
     * <code>
     * $ mytool --file
     * </code>
     * .
     *
     * The newly created parameter contains only it's 2 names and each other 
     * attribute is set to it's default value. You can simply manipulate
     * those attributes by accessing them directly.
     * 
     * @param string $short      Short name of the parameter without '-' (eg. 'f').
     * @param string $long       Long name of the parameter without '--' (eg. 'file').
     * @param int $type          Value type of the parameter. One of ezcConsoleInput::TYPE_*.
     * @param mixed $default     Default value the parameter holds if not submitted.
     * @param bool $multiple     If the parameter may be submitted multiple times.
     * @param string $shorthelp  Short help text.
     * @param string $longhelp   Long help text.
     * @param array(ezcConsoleOptionRule) $dependencies Dependency rules.
     * @param array(ezcConsoleOptionRule) $exclusions   Exclusion rules.
     * @param bool $arguments    Whether supplying arguments is allowed when this parameter is set.
     * @param bool $mandatory    Whether the parameter must be always submitted.
     * @param bool $isHelpOption Indicates that the given parameter is a help 
     *                           option. If a help option is set, all rule 
     *                           checking is skipped (dependency/exclusion/
     *                           mandatory).
     *
     * @throws ezcConsoleInvalidOptionNameException If the option names start with a "-" 
     *                                              sign or contain whitespaces.
     */
    public function __construct( 
        $short = '', 
        $long, 
        $type = ezcConsoleInput::TYPE_NONE, 
        $default = null, 
        $multiple = false,
        $shorthelp = 'No help available.',
        $longhelp = 'Sorry, there is no help text available for this parameter.', 
        array $dependencies = array(),
        array $exclusions = array(), 
        $arguments = true,
        $mandatory = false,
        $isHelpOption = false
    )
    {
        $this->properties['short'] = '';
        $this->properties['long'] = '';
        $this->properties['arguments'] = $arguments;

        if ( !self::validateOptionName( $short ) )
        {
            throw new ezcConsoleInvalidOptionNameException( $short );
        }
        $this->properties['short'] = $short;
        
        if ( !self::validateOptionName( $long ) )
        {
            throw new ezcConsoleInvalidOptionNameException( $long );
        }
        $this->properties['long'] = $long;
        
        $this->__set( "type",      $type         !== null ? $type      : ezcConsoleInput::TYPE_NONE  );
        $this->__set( "multiple",  $multiple     !== null ? $multiple  : false  );
        $this->__set( "default",   $default      !== null ? $default   : null );
        $this->__set( "shorthelp", $shorthelp    !== null ? $shorthelp : 'No help available.' );
        $this->__set( "longhelp",  $longhelp     !== null ? $longhelp  : 'Sorry, there is no help text available for this parameter.' );
        
        $dependencies    = $dependencies !== null && is_array( $dependencies ) ? $dependencies : array();
        foreach ( $dependencies as $dep )
        {
            $this->addDependency( $dep );
        }
        
        $exclusions = $exclusions !== null && is_array( $exclusions ) ? $exclusions : array();
        foreach ( $exclusions as $exc )
        {
            $this->addExclusion( $exc );
        }

        $this->__set( "mandatory",    $mandatory !== null ? $mandatory : false );
        $this->__set( "isHelpOption", $isHelpOption !== null ? $isHelpOption : false );
    }

    /**
     * Add a new dependency for a parameter.
     * This registeres a new dependency rule with the parameter. If you try
     * to add an already registered rule it will simply be ignored. Else,
     * the submitted rule will be added to the parameter as a dependency.
     *
     * @param ezcConsoleOptionRule $rule The rule to add.
     * @return void
     */
    public function addDependency( ezcConsoleOptionRule $rule )
    {
        foreach ( $this->dependencies as $existRule )
        {
            if ( $rule == $existRule )
            {
                return;
            }
        }
        $this->dependencies[] = $rule;
    }
    
    /**
     * Remove a dependency rule from a parameter.
     * This removes a given rule from a parameter, if it exists. If the rule is
     * not registered with the parameter, the method call will simply be ignored.
     * 
     * @param ezcConsoleOptionRule $rule The rule to be removed.
     * @return void
     */
    public function removeDependency( ezcConsoleOptionRule $rule )
    {
        foreach ( $this->dependencies as $id => $existRule )
        {
            if ( $rule == $existRule )
            {
                unset( $this->dependencies[$id] );
            }
        }
    }
    
    /**
     * Remove all dependency rule referring to a parameter.
     * This removes all dependency rules from a parameter, that refer to as specific 
     * parameter. If no rule is registered with this parameter as reference, the 
     * method call will simply be ignored.
     * 
     * @param ezcConsoleOption $param The param to be check for rules.
     * @return void
     */
    public function removeAllDependencies( ezcConsoleOption $param )
    {
        foreach ( $this->dependencies as $id => $rule )
        {
            if ( $rule->option == $param )
            {
                unset( $this->dependencies[$id] );
            }
        }
    }
    
    /**
     * Returns if a dependency to the given option exists.
     * Returns true if a dependency rule to the given option is registered,
     * otherwise false.
     * 
     * @param ezcConsoleOption $param The param to check if a dependency exists to.
     * @return bool True if rule is registered, otherwise false.
     */
    public function hasDependency( ezcConsoleOption $param )
    {
        foreach ( $this->dependencies as $id => $rule )
        {
            if ( $rule->option == $param )
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Returns the dependency rules registered with this parameter.
     * Returns an array of registered dependencies.
     *
     * For example:
     * <code>
     * array(
     *      0 => ezcConsoleOptionRule,
     *      1 => ezcConsoleOptionRule,
     *      2 => ezcConsoleOptionRule,
     * );
     * </code>
     * 
     * @return array(ezcConsoleOptionRule) Dependency definition.
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Reset existing dependency rules.
     * Deletes all registered dependency rules from the option definition.
     * 
     * @return void
     */
    public function resetDependencies() 
    {
        $this->dependencies = array();
    }

    /**
     * Add a new exclusion for an option.
     * This registeres a new exclusion rule with the option. If you try
     * to add an already registered rule it will simply be ignored. Else,
     * the submitted rule will be added to the option as a exclusion.
     *
     * @param ezcConsoleOptionRule $rule The rule to add.
     * @return void
     */
    public function addExclusion( ezcConsoleOptionRule $rule )
    {
        foreach ( $this->exclusions as $existRule )
        {
            if ( $rule == $existRule )
            {
                return;
            }
        }
        $this->exclusions[] = $rule;
    }
    
    /**
     * Remove a exclusion rule from a option.
     * This removes a given rule from a option, if it exists. If the rule is
     * not registered with the option, the method call will simply be ignored.
     * 
     * @param ezcConsoleOptionRule $rule The rule to be removed.
     * @return void
     */
    public function removeExclusion( ezcConsoleOptionRule $rule )
    {
        foreach ( $this->exclusions as $id => $existRule )
        {
            if ( $rule == $existRule )
            {
                unset( $this->exclusions[$id] );
            }
        }
    }
    
    /**
     * Remove all exclusion rule referring to a option.
     * This removes all exclusion rules from a option, that refer to as specific 
     * option. If no rule is registered with this option as reference, the 
     * method call will simply be ignored.
     * 
     * @param ezcConsoleOption $param The option to remove rule for.
     * @return void
     */
    public function removeAllExclusions( ezcConsoleOption $param )
    {
        foreach ( $this->exclusions as $id => $rule )
        {
            if ( $rule->option == $param )
            {
                unset( $this->exclusions[$id] );
            }
        }
    }
    
    /**
     * Returns if a given exclusion rule is registered with the option.
     * Returns true if a exclusion rule to the given option is registered,
     * otherwise false.
     * 
     * @param ezcConsoleOption $param The param to check if exclusions exist for.
     * @return bool True if rule is registered, otherwise false.
     */
    public function hasExclusion( ezcConsoleOption $param )
    {
        foreach ( $this->exclusions as $id => $rule )
        {
            if ( $rule->option == $param )
            {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Returns the exclusion rules registered with this parameter.
     * Returns an array of registered exclusions.
     *
     * For example:
     * <code>
     * array(
     *      0 => ezcConsoleOptionRule,
     *      1 => ezcConsoleOptionRule,
     *      2 => ezcConsoleOptionRule,
     * );
     * </code>
     * 
     * @return array(ezcConsoleOptionRule) Exclusions definition.
     */
    public function getExclusions()
    {
        return $this->exclusions;
    }

    /**
     * Reset existing exclusion rules.
     * Deletes all registered exclusion rules from the option definition.
     *
     * @return void
     */
    public function resetExclusions() 
    {
        $this->exclusions = array();
    }
    
    /**
     * Property read access.
     * Provides read access to the properties of the object.
     * 
     * @param string $key The name of the property.
     * @return mixed The value if property exists and isset, otherwise null.
     * @ignore
     */
    public function __get( $key )
    {
        switch ( $key  )
        {
            case 'short':
            case 'long':
            case 'type':
            case 'default':
            case 'multiple':
            case 'shorthelp':
            case 'longhelp':
            case 'arguments':
            case 'isHelpOption':
            case 'mandatory':
                return $this->properties[$key];
            case 'dependencies':
            default:
                throw new ezcBasePropertyNotFoundException( $key );
        }
    }

    /**
     * Property write access.
     * 
     * @param string $key Name of the property.
     * @param mixed $val  The value for the property.
     *
     * @throws ezcBasePropertyPermissionException
     *         If the property you try to access is read-only.
     * @throws ezcBasePropertyNotFoundException 
     *         If the the desired property is not found.
     * @ignore
     */
    public function __set( $key, $val )
    {
        switch ( $key )
        {
            case 'type':
                if ( $val !== ezcConsoleInput::TYPE_NONE 
                     && $val !== ezcConsoleInput::TYPE_INT 
                     && $val !== ezcConsoleInput::TYPE_STRING )
                {
                    throw new ezcBaseValueException( 
                        $key,  
                        $val, 
                        'ezcConsoleInput::TYPE_STRING, ezcConsoleInput::TYPE_INT or ezcConsoleInput::TYPE_NONE' 
                    );
                }
                break;
            case 'default':
                if ( ( is_scalar( $val ) === false && $val !== null ) )
                {
                    // Newly allow arrays, if multiple is true
                    if ( $this->multiple === true && is_array( $val ) === true )
                    {
                        break;
                    }
                    throw new ezcBaseValueException( $key, $val, 'a string or a number, if multiple == true also an array' );
                }
                break;
            case 'multiple':
                if ( !is_bool( $val ) )
                {
                    throw new ezcBaseValueException( $key, $val, 'bool' );
                }
                break;
            case 'shorthelp':
                if ( !is_string( $val ) )
                {
                    throw new ezcBaseValueException( $key, $val, 'string' );
                }
                break;
            case 'longhelp':
                if ( !is_string( $val ) )
                {
                    throw new ezcBaseValueException( $key, $val, 'string' );
                }
                break;
            case 'arguments':
                if ( !is_bool( $val ) )
                {
                    throw new ezcBaseValueException( $key, $val, 'bool' );
                }
                break;
            case 'mandatory':
                if ( !is_bool( $val ) )
                {
                    throw new ezcBaseValueException( $key, $val, 'bool' );
                }
                break;
            case 'isHelpOption':
                if ( !is_bool( $val ) )
                {
                    throw new ezcBaseValueException( $key, $val, 'bool' );
                }
                break;
            case 'long':
            case 'short':
                throw new ezcBasePropertyPermissionException( $key, ezcBasePropertyPermissionException::READ );
                break;
            default:
                throw new ezcBasePropertyNotFoundException( $key );
                break;
        }
        $this->properties[$key] = $val;
    }
 
    /**
     * Property isset access.
     * 
     * @param string $key Name of the property.
     * @return bool True is the property is set, otherwise false.
     * @ignore
     */
    public function __isset( $key )
    {
        switch ( $key  )
        {
            case 'short':
            case 'long':
            case 'type':
            case 'default':
            case 'multiple':
            case 'shorthelp':
            case 'longhelp':
            case 'arguments':
            case 'isHelpOption':
            case 'mandatory':
                return ( $this->properties[$key] !== null );
        }
        return false;
    }

    /**
     * Returns if a given name if valid for use as a parameter name a parameter. 
     * Checks if a given parameter name is generally valid for use. It checks a)
     * that the name does not start with '-' or '--' and b) if it contains
     * whitespaces. Note, that this method does not check any conflicts with already
     * used parameter names.
     * 
     * @param string $name The name to check.
     * @return bool True if the name is valid, otherwise false.
     */
    public static function validateOptionName( $name )
    {
        if ( substr( $name, 0, 1 ) === '-' || strpos( $name, ' ' ) !== false )
        {
            return false;
        }
        return true;
    }
}

?>
