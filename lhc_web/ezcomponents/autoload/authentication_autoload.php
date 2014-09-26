<?php
/**
 * Autoloader definition for the Authentication component.
 *
 * @copyright Copyright (C) 2005-2009 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version 1.3.1
 * @filesource
 * @package Authentication
 */

return array(
    'ezcAuthenticationException'                         => 'Authentication/exceptions/authentication_exception.php',
    'ezcAuthenticationOpenidException'                   => 'Authentication/exceptions/openid_exception.php',
    'ezcAuthenticationTypekeyException'                  => 'Authentication/exceptions/typekey_exception.php',
    'ezcAuthenticationGroupException'                    => 'Authentication/exceptions/group_exception.php',
    'ezcAuthenticationLdapException'                     => 'Authentication/exceptions/ldap_exception.php',
    'ezcAuthenticationOpenidConnectionException'         => 'Authentication/exceptions/openid_connection_exception.php',
    'ezcAuthenticationOpenidModeNotSupportedException'   => 'Authentication/exceptions/openid_mode_exception.php',
    'ezcAuthenticationOpenidRedirectException'           => 'Authentication/exceptions/openid_redirect_exception.php',
    'ezcAuthenticationTypekeyPublicKeysInvalidException' => 'Authentication/exceptions/typekey_invalid_exception.php',
    'ezcAuthenticationTypekeyPublicKeysMissingException' => 'Authentication/exceptions/typekey_missing_exception.php',
    'ezcAuthenticationBignumLibrary'                     => 'Authentication/math/bignum_library.php',
    'ezcAuthenticationCredentials'                       => 'Authentication/credentials/credentials.php',
    'ezcAuthenticationDataFetch'                         => 'Authentication/interfaces/data_fetch.php',
    'ezcAuthenticationFilter'                            => 'Authentication/interfaces/authentication_filter.php',
    'ezcAuthenticationFilterOptions'                     => 'Authentication/options/filter_options.php',
    'ezcAuthenticationOpenidStore'                       => 'Authentication/filters/openid/openid_store.php',
    'ezcAuthenticationOpenidStoreOptions'                => 'Authentication/options/openid_store_options.php',
    'ezcAuthentication'                                  => 'Authentication/authentication.php',
    'ezcAuthenticationBcmathLibrary'                     => 'Authentication/math/bcmath_library.php',
    'ezcAuthenticationGmpLibrary'                        => 'Authentication/math/gmp_library.php',
    'ezcAuthenticationGroupFilter'                       => 'Authentication/filters/group/group_filter.php',
    'ezcAuthenticationGroupOptions'                      => 'Authentication/options/group_options.php',
    'ezcAuthenticationHtpasswdFilter'                    => 'Authentication/filters/htpasswd/htpasswd_filter.php',
    'ezcAuthenticationHtpasswdOptions'                   => 'Authentication/options/htpasswd_options.php',
    'ezcAuthenticationIdCredentials'                     => 'Authentication/credentials/id_credentials.php',
    'ezcAuthenticationLdapFilter'                        => 'Authentication/filters/ldap/ldap_filter.php',
    'ezcAuthenticationLdapInfo'                          => 'Authentication/filters/ldap/ldap_info.php',
    'ezcAuthenticationLdapOptions'                       => 'Authentication/options/ldap_options.php',
    'ezcAuthenticationMath'                              => 'Authentication/math/math.php',
    'ezcAuthenticationOpenidAssociation'                 => 'Authentication/filters/openid/openid_association.php',
    'ezcAuthenticationOpenidFileStore'                   => 'Authentication/filters/openid/openid_file_store.php',
    'ezcAuthenticationOpenidFileStoreOptions'            => 'Authentication/options/openid_file_store_options.php',
    'ezcAuthenticationOpenidFilter'                      => 'Authentication/filters/openid/openid_filter.php',
    'ezcAuthenticationOpenidOptions'                     => 'Authentication/options/openid_options.php',
    'ezcAuthenticationOptions'                           => 'Authentication/options/authentication_options.php',
    'ezcAuthenticationPasswordCredentials'               => 'Authentication/credentials/password_credentials.php',
    'ezcAuthenticationSession'                           => 'Authentication/session/authentication_session.php',
    'ezcAuthenticationSessionOptions'                    => 'Authentication/options/session_options.php',
    'ezcAuthenticationStatus'                            => 'Authentication/status/authentication_status.php',
    'ezcAuthenticationTokenFilter'                       => 'Authentication/filters/token/token_filter.php',
    'ezcAuthenticationTokenOptions'                      => 'Authentication/options/token_options.php',
    'ezcAuthenticationTypekeyFilter'                     => 'Authentication/filters/typekey/typekey_filter.php',
    'ezcAuthenticationTypekeyOptions'                    => 'Authentication/options/typekey_options.php',
    'ezcAuthenticationUrl'                               => 'Authentication/url/url.php',
);
?>
