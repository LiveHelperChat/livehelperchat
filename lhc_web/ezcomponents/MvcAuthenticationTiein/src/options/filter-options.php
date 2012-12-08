<?php
/**
 * File containing the ezcMvcAuthenticationFilterOptions class
 *
 * @package MvcAuthenticationTiein
 * @version 1.0
 * @copyright Copyright (C) 2005-2009 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Class containing authentication filter options
 *
 * @property ezcDbInstance $database      The database that is used for the user database.
 * @property string $tableName            The table that stores the user information.
 * @property string $userIdField          The field that contains the unique user ID.
 * @property string $userNameField        The field that contains the user's full name.
 * @property string $passwordField        The field that contains the user's password.
 * @property string $varNameFilter        The name of the variable under which the auth filter is available in the controller's actions.
 * @property string $varNameUserName      The name of the variable under which the user name will be provided in the controller's actions.
 * @property string $varNameUserId        The name of the variable under which the user ID will be provided in the controller's actions.
 * @property string $sessionUserIdKey     The name of the session variable that contains the user ID.
 * @property string $sessionTimestampKey  The name of the session variable that contains the last-accessed timestamp.
 * @property string $loginRequiredUri     The URI that the filter will be redirected to when authentication is required.
 * @property string $logoutUri            The URI that the filter will be redirected to when he runs the logout action.
 *
 * @package MvcAuthenticationTiein
 * @version 1.0
 */
class ezcMvcAuthenticationFilterOptions extends ezcBaseOptions
{
    /**
     * Constructs an object with the specified values.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if $options contains a property not defined
     * @throws ezcBaseValueException
     *         if $options contains a property with a value not allowed
     * @param array(string=>mixed) $options
     */
    public function __construct( array $options = array() )
    {
        $this->tableName = 'user';
        $this->userIdField = 'email';
        $this->userNameField = 'name';
        $this->passwordField = 'password';
        $this->database = ezcDbInstance::get();

        $this->varNameFilter   = 'ezcAuth_filter';
        $this->varNameUserName = 'ezcAuth_user_name';
        $this->varNameUserId   = 'ezcAuth_user_id';

        $this->sessionUserIdKey = 'ezcAuth_id';
        $this->sessionTimestampKey = 'ezcAuth_timestamp';

        $this->loginRequiredUri = '/login-required';
        $this->logoutUri = '/';

        parent::__construct( $options );
    }

    /**
     * Sets the option $name to $value.
     *
     * @throws ezcBasePropertyNotFoundException
     *         if the property $name is not defined
     * @throws ezcBaseValueException
     *         if $value is not correct for the property $name
     * @param string $name
     * @param mixed $value
     * @ignore
     */
    public function __set( $name, $value )
    {
        switch ( $name )
        {
            case 'database':
                if ( !$value instanceof ezcDbHandler )
                {
                    throw new ezcBaseValueException( $name, $value, 'ezcDbInstance' );
                }
                $this->properties[$name] = $value;
                break;
            case 'tableName':
            case 'userIdField':
            case 'userNameField':
            case 'passwordField':
            case 'varNameFilter':
            case 'varNameUserName':
            case 'varNameUserId':
            case 'sessionUserIdKey':
            case 'sessionTimestampKey':
            case 'loginRequiredUri':
            case 'logoutUri':
                if ( !is_string( $value ) )
                {
                    throw new ezcBaseValueException( $name, $value, 'string' );
                }
                $this->properties[$name] = $value;
                break;

            default:
                throw new ezcBasePropertyNotFoundException( $name );
        }
    }
}
?>
